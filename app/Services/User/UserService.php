<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use ErrorException;

class UserService {

    public function __construct(
        protected UserRepositoryInterface $userRepository
    ){}
    
    /**
     * Finds the user from a crypted state from the external provider
     * @param string $crypted_encoded_state
     * @return User
     */
    public function findByCriptedState(string $crypted_encoded_state) : User
    {
        $state_decrypted = \json_decode(\base64_decode($crypted_encoded_state));

        if(!$state_decrypted) throw new ErrorException("teste");

        return $this->userRepository->find($state_decrypted["user_id"]);
    }
}