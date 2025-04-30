<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface {
    
    /**
     * Creates a new User
     * @param Array $insert_data
     * @return User
     */
    public function create(Array $insert_data) : User;

     /**
     * Finds a User by the id
     * @param Array $insert_data
     * @return User
     */
    public function find(String $id) : User;

    /**
     * Checks if any of the given emails exist in the database
     * @param array $emails
     * @return array
     */
    public function getExistingEmails(array $emails);
}