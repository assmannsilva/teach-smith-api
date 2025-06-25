<?php

namespace App\Http\Controllers\Users\Invites;

use App\Http\Controllers\Controller;

abstract class BaseInviteController extends Controller
{

    protected function calculateStatusCode(array $result)
    {
        $check_errors = \array_filter($result["errors"], fn($error) => $error > 0);
        $status_code = match (true) {
            count($check_errors) != 0 && $result["dispatched"] > 0 => 207,
            count($check_errors) == 0 && $result["dispatched"] > 0 => 200,
            default => 422,
        };

        return $status_code;
    }

    protected function dataErrorMessage(int $total_data_errors)
    {
        if($total_data_errors == 0) return "";
        return "$total_data_errors rows contain incorrect or missing data out";
    }
    
    protected function alreadyRegisteredUsersMessage(?int $total_email_errors = null)
    {
        if($total_email_errors == null) return "email already registered";
        if($total_email_errors == 0) return "";
        return "$total_email_errors emails are already registered between valid rows";
    }
}
