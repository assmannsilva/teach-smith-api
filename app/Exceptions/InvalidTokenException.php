<?php

namespace App\Exceptions;

use Exception;

class InvalidTokenException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param string $message
     */
    public function __construct($message = 'Invalid token')
    {
        parent::__construct($message);
    }

    public function render($request)
    {
        return response()->json([
            'error' => $this->getMessage()
        ], 400);
    }
}
