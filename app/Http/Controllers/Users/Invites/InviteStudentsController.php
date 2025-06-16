<?php

namespace App\Http\Controllers\Users\Invites;

use App\Http\Requests\Users\Invites\InviteStudentsRequest;
use App\Models\Student;
use App\Services\User\InviteUserService;

class InviteStudentsController extends BaseInviteController
{
    protected String $userModel = Student::class;
    protected String $formRequestValidation = InviteStudentsRequest::class;

    public function store(
        InviteStudentsRequest $request,
        InviteUserService $invite_user_service,  
    ) {
        return $this->handleStore(
            $request->input(),
            $invite_user_service,
        );  
    }
}
