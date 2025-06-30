<?php

namespace App\Exceptions;

use Exception;

class UserNotFoundException extends RegistrationException
{

    public function __construct(String $message)
    {
        parent::__construct(
           $message,
            404
        );
    }
}
