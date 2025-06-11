<?php

namespace App\Http\Controllers\Users\Invites;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Invites\BulkInviteTeachersRequest;
use App\Http\Requests\Users\Invites\InviteTeachersRequest;
use App\Imports\TeachersImport;
use App\Models\Teacher;
use App\Services\User\InviteUserService;

class InviteTeachersController extends Controller
{
    public function store(
        InviteTeachersRequest $request,
        InviteUserService $invite_user_service,  
    ) {
        $result = $invite_user_service->dispatchInvites([$request->input()],Teacher::class);
        return response()->json([
            "message" => $result["dispatched_count"] > 0 ? "Invite dispatched" : "Already registered email",
        ], $result["dispatched_count"] > 0 ? 200 : 400);  
    }

    public function import(
        BulkInviteTeachersRequest $request,
        InviteUserService $invite_user_service,
        TeachersImport $teachers_import
    ) {
        $result = $invite_user_service->importUsers(
            $teachers_import,
            $request->file("import_file"),
            Teacher::class
        );

        $status_code = match (true) {
            !empty($result["errors"]) && $result["dispatched_count"] > 0 => 207,
            empty($result["errors"]) && $result["dispatched_count"] > 0 => 200,
            default => 422,
        };

        return response()->json([
            ...$result,
            "messsage" => $result["dispatched_count"] > 0 ? "Invites dispatched" : "Could not dispatch invites",
        ],$status_code);
    }
}
