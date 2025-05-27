<?php

namespace App\Lib\AuthStrategy;

use App\Lib\AuthStrategy\Interfaces\AuthStrategyInterface;
use App\Models\User;
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
        return Auth::attempt($credentials);
    }
    
    /**
     * Completa o registro do usuário com a senha fornecida
     * @param User $user
     * @param string $auth_credential Senha do usuário
     * @return User
     */
    public function makeRegistration(User $user, string $auth_credential): User
    {
        $user->password = Hash::make($auth_credential);
        $this->userRepository->save($user);
        Auth::login($user);
        return $user;
    }
}