<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface {
    
    /**
     * Find an User by its ID.
     * @param String $id
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(String $id) : User;

    /**
     * Creates a new User
     * @param Array $insert_data
     * @return User
     */
    public function create(Array $insert_data) : User;
}