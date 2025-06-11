<?php

namespace App\Http\Controllers\Users\Invites;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Invites\BulkInviteTeachersRequest;
use App\Http\Requests\Users\Invites\InviteTeachersRequest;
use App\Models\Teacher;
use App\Services\User\InviteUserService;

class InviteTeachersController extends Controller
{
    public function store(
        InviteTeachersRequest $request,
        InviteUserService $invite_user_service,  
    ) {
        /*
        $result = $invite_user_service->dispatchInvites($request->input("teachers"),Teacher::class);
        //$csv_error_path = $invite_user_service->generateReportForDuplicatedEmails($result["duplicated_emails"]);

        return response()->json([
            "dispatched_count" => $result["dispatched_count"],
            "duplicated_count" => $result["duplicated_emails_count"],
            "messsage" => $result["dispatched_count"] > 0 ? "Invites dispatched" : "Already registered emails",
        ], $result["dispatched_count"] > 0 ? 200 : 400);
       
        */
        
    }

    public function import(
        BulkInviteTeachersRequest $request,
        InviteUserService $invite_user_service,  
    ) {
        $result = $invite_user_service->importUsers($request->file("import_file"),Teacher::class);
        $status_code = match (true) {
            !empty($result["errors"]) && $result["dispatched_count"] > 0 => 207,
            empty($result["errors"]) && $result["dispatched_count"] > 0 => 200,
            default => 422,
        };
        return response()->json([
            ...$result,
            "messsage" => $result["dispatched_count"] > 0 ? "Invites dispatched" : "Already registered emails",
        ],$status_code);
    }
}
