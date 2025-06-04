<?php
namespace App\Repositories;

use App\Enums\ProvidersEnum;
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
        return $this->newQuery()
        ->whereIn('email', $emails)
        ->pluck("email")
        ->toArray();
    }

    /**
     * Finds a user by their provider credentials
     * @param ProvidersEnum $provider
     * @param string $provider_id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return User|null
     */
    public function findByProviderCredentials(ProvidersEnum $provider, string $provider_id) : ?User
    {
        return $this->newQuery()
        ->where('provider', $provider)
        ->where('provider_id', $provider_id)
        ->where("active",true)
        ->firstOrFail();
    }
    
    /**
     * Finds a user by their email address
     * @param string $email
     * @param bool $fail = false
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return User|null
     */
    public function findByEmail(string $email, bool $fail = false) : ?User
    {
        $email_key_index = SodiumCrypto::getCryptKey("app.crypted_columns.users.email_index");
        $email_index = SodiumCrypto::getIndex($email,$email_key_index);
        $query = $this->newQuery()->where('email_index', $email_index)->where("active",true);

        return $fail ? $query->firstOrFail() : $query->first();
    }
}