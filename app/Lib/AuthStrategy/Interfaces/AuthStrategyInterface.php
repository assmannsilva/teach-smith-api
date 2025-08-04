<?php
namespace App\Lib\AuthStrategy\Interfaces;

use App\Models\User;

interface AuthStrategyInterface
{   
    /**
     * Completes the registration of a user with the provided credential.
     * @param User $user
     * @param string $auth_credential (password or code) 
     * @param ?string $state (extra data and  CSRF protection)
     */
    public function makeRegistration(User $user, string $auth_credential, ?string $state = null) : User;

    /**
     *  Performs authentication
     * @param array $credentials (email/password or code, etc.)
     */
    public function authenticate(array $credentials) : bool;
}