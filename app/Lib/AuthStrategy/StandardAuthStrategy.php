<?php

namespace App\Lib\AuthStrategy;

use App\Lib\AuthStrategy\Interfaces\AuthStrategyInterface;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StandardAuthStrategy implements AuthStrategyInterface {

    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) { }
    
    /**
     * Tenta realizar o login do usuário com as credenciais fornecidas
     * @param array $credentials
     * @return bool
     */
    public function authenticate(array $credentials): bool
    {
        $user = $this->userRepository->findByEmail($credentials['email'],true);
        if($user && Hash::check($credentials["password"],$user->password)) {
            Auth::login($user);
            return true;
        }

        return false;
    }
    
    /**
     * Completes the registration of a user with the provided credential.
     * @param User $user
     * @param string $auth_credential user password
     * @param ?string $state (não utilizada nessa implementação)
     * @return User
     */
    public function makeRegistration(User $user, string $auth_credential, ?string $state = null): User
    {
        if($user_finded = $this->userRepository->findByEmail($user->email)) {
            throw new \App\Exceptions\UserAlreadyRegisteredException($user_finded);
        }

        $user->password = Hash::make($auth_credential);
        $this->userRepository->save($user);
        Auth::login($user);
        return $user;
    }
}