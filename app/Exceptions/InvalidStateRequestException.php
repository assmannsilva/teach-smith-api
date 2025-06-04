<?php

namespace App\Exceptions;

use Exception;

class InvalidStateRequestException extends Exception
{
        public function render($request)
    {
        return response()->json([
            'error' => 'Invalid state request.'
        ], 400);
    }
}
