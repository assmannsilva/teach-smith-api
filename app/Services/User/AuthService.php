<?php

namespace App\Services\User;

use App\Lib\LoginStrategy\Interfaces\AuthStrategyInterface;
use App\Lib\LoginStrategy\Interfaces\ExternalProviderInterface;
use App\Models\User;

class AuthService
{

    public function authenticate(
        array $credentials,
        AuthStrategyInterface $auth_strategy
    ) {
        $auth_strategy->authenticate($credentials);
    }

    public function completeRegistration(
        User $user,
        string $new_credential,
        AuthStrategyInterface $auth_strategy
    ) {
        $user = $auth_strategy->makeRegistration($user, $new_credential);
        return $user;
    }

    public function generateOAuthUrl(ExternalProviderInterface $auth_strategy)
    {
        return $auth_strategy->generateOAuthUrl();
    }
}