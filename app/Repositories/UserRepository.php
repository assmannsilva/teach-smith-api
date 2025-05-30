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

    public function getExistingEmails(array $emails) : array
    {
        return $this->newQuery()
        ->whereIn('email', $emails)
        ->pluck("email")
        ->toArray();
    }

    public function findByProviderCredentials(ProvidersEnum $provider, string $provider_id) : ?User
    {
        return $this->newQuery()
        ->where('provider', $provider)
        ->where('provider_id', $provider_id)
        ->where("active",true)
        ->first();
    }

    public function findByEmail(string $email) : ?User
    {
        $email_key_index = SodiumCrypto::getCryptKey("app.crypted_columns.users.email_index");
        $email_index = SodiumCrypto::getIndex($email,$email_key_index);
        return $this->newQuery()
        ->where('email_index', $email_index)
        ->where("active",true)
        ->first();
    }
}