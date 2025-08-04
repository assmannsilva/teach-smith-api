<?php

namespace App\Services\User;

use App\Exceptions\InvalidStateRequestException;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

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
        $state_decrypted = \json_decode(\base64_decode($crypted_encoded_state),true);
        if(!$state_decrypted) throw new InvalidStateRequestException;

        return $this->userRepository->find($state_decrypted["user_id"]);
    }
}