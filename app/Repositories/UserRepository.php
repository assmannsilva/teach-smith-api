<?php
namespace App\Repositories;

use App\Enums\ProvidersEnum;
use App\Exceptions\UserNotFoundException;
use App\Helpers\SodiumCrypto;
use App\Models\Organization;
use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface {

    protected $modelClass = User::class;

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
}