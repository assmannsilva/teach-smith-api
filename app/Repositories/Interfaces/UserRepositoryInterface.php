<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface {
    /**
     * Checks if any of the given emails exist in the database
     * @param array $emails
     * @return array
     */
    public function getExistingEmails(array $emails);
}