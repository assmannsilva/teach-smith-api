<?php
namespace App\Lib\AuthStrategy\Interfaces;

use App\Models\User;

interface AuthStrategyInterface
{   
    /**
     * @param User $user
     * @param string $auth_credential (senha ou code) 
     * @param ?string $state (dados extras e proteção CSRF)
     */
    public function makeRegistration(User $user, string $auth_credential, ?string $state = null) : User;

    /**
     * @param array $credentials Dados necessários para autenticação (email/senha, ou code, etc.)
     */
    public function authenticate(array $credentials) : bool;
}