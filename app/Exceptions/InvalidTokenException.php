<?php

namespace App\Exceptions;

class InvalidTokenException extends RegistrationException
{
    /**
     * Create a new exception instance.
     *
     * @param string $message
     */
    public function __construct($message = 'Invalid token')
    {
        parent::__construct($message,400);
    }
}
