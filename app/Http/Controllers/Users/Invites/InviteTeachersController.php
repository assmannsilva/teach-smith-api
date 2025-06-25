<?php

namespace App\Http\Controllers\Users\Invites;

use App\Lib\DispatchUserInvites\DispatchTeacherInvites;
use App\Http\Requests\Users\Invites\BulkInviteRequest;
use App\Http\Requests\Users\Invites\InviteTeachersRequest;
use App\Services\User\InviteUserService;

class InviteTeachersController extends BaseInviteController
{

    public function store(
        InviteTeachersRequest $request,
        InviteUserService $invite_user_service,  
        DispatchTeacherInvites $dispatch_teacher_invites
    ) {
        $result = $invite_user_service->dispatchSingleInvite($request->input(),$dispatch_teacher_invites);
        $response_message = match (true) {
            $result["errors"]["total_duplicated"] > 0 => $this->alreadyRegisteredUsersMessage(),
            default => "Invite dispatched"
        };

        return \response([
           "message" => $response_message
        ],$this->calculateStatusCode($result));  
    }

    public function import(
        BulkInviteRequest $request,
        InviteUserService $invite_user_service,
        DispatchTeacherInvites $dispatch_teacher_invites
    ) {
        $result = $invite_user_service->dispatchMultipleInvites(
            $request->file("import_file"),
            $dispatch_teacher_invites
        );

        $errors = \array_filter([
            $this->dataErrorMessage($result["errors"]["total_data_errors"]),
            $this->alreadyRegisteredUsersMessage($result["errors"]["total_duplicated"]),
        ]);

        return \response([
           "dispatched" => $result["dispatched"],
            "errors" => $errors
        ],$this->calculateStatusCode($result));
    }
}
