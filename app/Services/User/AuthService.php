<?php

namespace App\Services\User;

use App\Enums\ProvidersActionsEnum;
use App\Enums\RolesEnum;
use App\Lib\AuthStrategy\Interfaces\AuthStrategyInterface;
use App\Lib\AuthStrategy\Interfaces\ExternalProviderInterface;
use App\Models\User;

class AuthService
{
    public function __construct(
        protected UserService $userService
    ) { }
    
    /**
     * Performs authentication using the provided strategy.
     * @param array $credentials
     * @param AuthStrategyInterface $strategy
     * @return bool
     */
    public function authenticate(
        array $credentials,
        AuthStrategyInterface $strategy
    ): bool 
    {
        return $strategy->authenticate($credentials);
    }

    /**
     * Registers a new user with the provided data and credential.
     * @param ?array $registration_data
     * @param string $new_credential
     * @param ?string $state
     * @param AuthStrategyInterface $strategy
     * @return User
     */
    public function register(
        ?array $registration_data,
        string $new_credential,
        ?string $state,
        AuthStrategyInterface $strategy
    ) : User 
    {
        $user = new User($registration_data ?? []);
        $user->role = RolesEnum::ADMIN;
        $user = $strategy->makeRegistration($user, $new_credential,$state);
        return $user;
    }

    /**
     * Registers an invited user with the provided credential.
     * @param User $user
     * @param string $new_credential
     * @param AuthStrategyInterface $auth_strategy
     * @return User
     */
    public function registerInvitedUser(
        User $user,
        string $new_credential,
        ?string $state,
        AuthStrategyInterface $auth_strategy
    ) : User
    {
        $user = $auth_strategy->makeRegistration($user, $new_credential, $state);
        return $user;
    } 

    /**
     * Generates an OAuth URL for the specified action and strategy.
     * @param ProvidersActionsEnum $action
     * @param ExternalProviderInterface $strategy
     * @param array $extras
     * @return string
     */
    public function generateOAuthUrl(
        ProvidersActionsEnum $action,
        ExternalProviderInterface $strategy,
        array $extras = []
    ) : string 
    {
        return $strategy->generateOAuthUrl($action, $extras);
    }
}