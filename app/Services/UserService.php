<?php

namespace App\Services;

use App\Enums\RolesEnum;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService {

    public function __construct(
        protected UserRepositoryInterface $user_repository_interface
    ){}
    
    /**
     * @param Array $insert_data
     * @return User
     */
    public function registerAdminUser($insert_data) : User
    {
        return $this->user_repository_interface->create([
            ...$insert_data,
            'password' => Hash::make($insert_data["password"]),
            'active' => true,
            'role' => RolesEnum::ADMIN,
        ]);
    }
}