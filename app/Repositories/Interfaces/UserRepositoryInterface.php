<?php

namespace App\Repositories\Interfaces;

use App\Enums\ProvidersEnum;
use App\Models\User;

interface UserRepositoryInterface {

    /**
     * Checks if any of the given emails exist in the database
     * @param array $emails
     * @return array
     */
    public function getExistingEmails(array $emails);

    /**
     * Finds a user by their email address
     * @param string $email
     * @param bool $fail = false
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return User|null
     */
    public function findByEmail(string $email, bool $fail = false) : ?User;


    /**
     * Finds a user by their provider credentials
     * @param ProvidersEnum $provider
     * @param string $provider_id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return User|null
     */
    public function findByProviderCredentials(ProvidersEnum $provider, string $provider_id) : ?User;
}