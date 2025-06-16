<?php

namespace App\Http\Controllers\Users\Invites;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Invites\BulkInviteRequest;
use App\Imports\UsersImport;
use App\Services\User\InviteUserService;

abstract class BaseInviteController extends Controller
{
    protected String $userModel;
    protected String $formRequestValidation;

    protected function handleStore(
        array $validated_data,
        InviteUserService $invite_user_service,  
    ) {
        $result = $invite_user_service->dispatchInvites([$validated_data],$this->userModel);
        return response()->json([
            "message" => $result["dispatched_count"] > 0 ? "Invite dispatched" : "Already registered email",
        ], $result["dispatched_count"] > 0 ? 200 : 400);  
    }

    public function import(
        BulkInviteRequest $request,
        InviteUserService $invite_user_service,
        UsersImport $users_import
    ) {
        $users_import->setFormRequestValidation($this->formRequestValidation);
        $result = $invite_user_service->importUsers(
            $users_import,
            $request->file("import_file"),
            $this->userModel
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
