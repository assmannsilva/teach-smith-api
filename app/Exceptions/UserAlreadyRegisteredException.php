<?php

namespace App\Exceptions;

use App\Models\User;
use Exception;

class UserAlreadyRegisteredException extends Exception
{
    
    public function __construct(User $user)
    {
        parent::__construct(
            "User with email {$user->email} is already registered.",
            409
        );

    }

    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage()
        ], 409);
    }
}
