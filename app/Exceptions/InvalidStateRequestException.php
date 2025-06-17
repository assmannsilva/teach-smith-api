<?php

namespace App\Exceptions;

use Exception;

class InvalidStateRequestException extends Exception
{
        public function render($request)
    {
        return response()->json([
            'message' => 'Invalid state request.'
        ], 400);
    }
}
