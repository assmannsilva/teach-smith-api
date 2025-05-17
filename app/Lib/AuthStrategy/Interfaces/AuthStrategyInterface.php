<?php
namespace App\Lib\LoginStrategy\Interfaces;

use App\Models\User;

interface AuthStrategyInterface
{   
    /**
     * @param User $user
     * @param string $auth_credential (senha ou code) 
     */
    public function makeRegistration(User $user, string $auth_credential) : User;

    /**
     * @param array $credentials Dados necessários para autenticação (email/senha, ou code, etc.)
     */
    public function authenticate(array $credentials) : bool;
}