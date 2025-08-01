<?php

namespace App\Repositories\Interfaces;

use App\Enums\ProvidersEnum;
use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

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
     * @throws UserNotFoundException
     * @return User|null
     */
    public function findByEmail(string $email, bool $fail = false) : ?User;


    /**
     * Finds a user by their provider credentials
     * @param ProvidersEnum $provider
     * @param string $provider_id
     * @throws UserNotFoundException
     * @return User
     */
    public function findByProviderCredentials(ProvidersEnum $provider, string $provider_id) : User;

    /**
     * Searches for users by name and role using encrypted indexes.
     *
     * @param string $name
     * @param RolesEnum $role
     * @param int $limit
     * @return Collection
     */
    public function searchByNameAndRole(string $name, RolesEnum $role, int $limit): Collection;
}