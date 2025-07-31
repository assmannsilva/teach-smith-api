<?php

namespace App\Http\Controllers\Users\Invites;

use App\Http\Requests\Users\Invites\BulkInviteRequest;
use App\Http\Requests\Users\Invites\InviteStudentsRequest;
use App\Lib\DispatchUserInvites\DispatchStudentInvites;
use App\Services\User\InviteUserService;

class InviteStudentsController extends BaseInviteController
{

    protected function classromsNonExistentErrorMessage(?int $total_non_existent_classrooms = null)
    {
        if($total_non_existent_classrooms == null) return "classroom do not exist";
        if($total_non_existent_classrooms == 0) return "";

        return "$total_non_existent_classrooms classrooms do not exist among eligible users.";
    }

    /**
     * Handle the invitation of a single student.
     *
     * @param InviteStudentsRequest $request
     * @param InviteUserService $invite_user_service
     * @param DispatchStudentInvites $dispatch_student_invites
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(
        InviteStudentsRequest $request,
        InviteUserService $invite_user_service,  
        DispatchStudentInvites $dispatch_student_invites
    ) {
        $result = $invite_user_service->dispatchSingleInvite($request->input(),$dispatch_student_invites);
        $response_message = match (true) {
            $result["errors"]["total_duplicated"] > 0 => $this->alreadyRegisteredUsersMessage(1),
            $result["errors"]["total_non_existing_classrooms"] > 0 => $this->classromsNonExistentErrorMessage(),
            default => "Invite dispatched"
        };

        return \response([
           "message" => $response_message
        ],$this->calculateStatusCode($result));  
    }

    /**
     * Handle the bulk import of student invites.
     *
     * @param BulkInviteRequest $request
     * @param InviteUserService $invite_user_service
     * @param DispatchStudentInvites $dispatch_student_invites
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(
        BulkInviteRequest $request,
        InviteUserService $invite_user_service,
        DispatchStudentInvites $dispatch_student_invites
    ) {
        $result = $invite_user_service->dispatchMultipleInvites(
            $request->file("import_file"),
            $dispatch_student_invites
        );

        $errors = \array_filter([
            $this->dataErrorMessage($result["errors"]["total_data_errors"]),
            $this->alreadyRegisteredUsersMessage($result["errors"]["total_duplicated"]),
            $this->classromsNonExistentErrorMessage($result["errors"]["total_non_existing_classrooms"]),
        ]);

        return \response([
           "dispatched" => $result["dispatched"],
            "errors" => $errors
        ],$this->calculateStatusCode($result));
    }
}
