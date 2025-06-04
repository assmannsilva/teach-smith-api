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
    
    public function authenticate(
        array $credentials,
        AuthStrategyInterface $strategy
    ) {
        return $strategy->authenticate($credentials);
    }

    public function register(
        ?array $registration_data,
        string $new_credential,
        ?string $state,
        AuthStrategyInterface $strategy
    ) {
        $user = new User($registration_data ?? []);
        $user->role = RolesEnum::ADMIN;
        $user = $strategy->makeRegistration($user, $new_credential,$state);
        return $user;
    }

    public function registerInvitedUser(
        User $user,
        string $new_credential,
        AuthStrategyInterface $auth_strategy
    ) {
        $user = $auth_strategy->makeRegistration($user, $new_credential);
        return $user;
    } 

    public function generateOAuthUrl(
        ProvidersActionsEnum $action,
        ExternalProviderInterface $strategy,
        array $extras = []
    ) : string 
    {
        return $strategy->generateOAuthUrl($action, $extras);
    }
}