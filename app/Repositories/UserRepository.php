<?php
namespace App\Repositories;

use App\Enums\ProvidersEnum;
use App\Enums\RolesEnum;
use App\Exceptions\UserNotFoundException;
use App\Helpers\SodiumCrypto;
use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface {

    protected $modelClass = User::class;

    /**
     * Applies a name-based index filter to the given query.
     *
     * @param string $name Full or partial name (first name, surnames, or both)
     * @param Builder $query The base query to apply filters to
     * @return Builder The updated query builder with filters applied
     */
    protected function filterByNameIndex(string $name, Builder $query) : Builder
    {
        $names = \explode(" ", $name);
        $first_name = $names[0];

        $first_name_key_index = SodiumCrypto::getCryptKey("app.crypted_columns.users.first_name_index");
        $surname_key_index = SodiumCrypto::getCryptKey("app.crypted_columns.users.surname_index");

        $first_name_index = SodiumCrypto::getIndex($first_name, $first_name_key_index);
        $names_indexes = \array_map(
            fn($name) => SodiumCrypto::getIndex($name, $surname_key_index), //Get all names (possibly first_name included)
            $names
        );
        
        return $query->where(function($sub_query) use($first_name_index,$names_indexes) {
            $sub_query->orWhere("first_name_index",$first_name_index);
            foreach ($names_indexes as $surname_index) {
                $sub_query->orWhereJsonContains("surname_tokens", $surname_index);
            }
        });
    }

    /**
     * Checks if any of the given emails exist in the database
     * @param array $emails
     * @return array
     */
    public function getExistingEmails(array $emails) : array
    {
        $email_key_index = SodiumCrypto::getCryptKey("app.crypted_columns.users.email_index");
        $emails_indexes = array_map(fn($email) => SodiumCrypto::getIndex($email, $email_key_index), $emails);

        return $this->newQuery()
        ->whereIn('email_index', $emails_indexes)
        ->pluck("email")
        ->toArray();
    }

    /**
     * Finds a user by their provider credentials
     * @param ProvidersEnum $provider
     * @param string $provider_id
     * @throws UserNotFoundException
     * @return User
     */
    public function findByProviderCredentials(ProvidersEnum $provider, string $provider_id) : User
    {
        $query = $this->newQuery()
        ->where('provider', $provider)
        ->where('provider_id', $provider_id)
        ->where("active",true)
        ->first();
        
        if (!$query) {
            throw new UserNotFoundException("User with the given provider credentials could not be found.");
        }

        return $query;
    }
    
    /**
     * Finds a user by their email address
     * @param string $email
     * @param bool $fail = false
     * @throws UserNotFoundException
     * @return User|null
     */
    public function findByEmail(string $email, bool $fail = false) : ?User
    {
        $email_key_index = SodiumCrypto::getCryptKey("app.crypted_columns.users.email_index");
        $email_index = SodiumCrypto::getIndex($email,$email_key_index);
        $query = $this->newQuery()->where('email_index', $email_index)->where("active",true);

        $user = $query->first();

        if ($fail && !$user) {
            throw new UserNotFoundException("User with the given email could not be found.");
        }

        return $user;
    }

    /**
     * Searches for users by name and role using encrypted indexes.
     *
     * @param string $name
     * @param RolesEnum $role
     * @param int $limit
     * @return Collection
     */
    public function searchByNameAndRole(string $name, RolesEnum $role, int $limit): Collection
    {
        $query = $this->newQuery();
        if($role) $query = $query->where("role",$role);

       
        return $this->filterByNameIndex($name,$query)
        ->limit($limit)
        ->select("id","first_name","surname")
        ->get();

    }



}