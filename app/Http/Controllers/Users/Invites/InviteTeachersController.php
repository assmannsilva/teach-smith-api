<?php

namespace App\Http\Controllers\Users\Invites;

use App\Http\Requests\Users\Invites\InviteTeachersRequest;
use App\Models\Teacher;
use App\Services\User\InviteUserService;

class InviteTeachersController extends BaseInviteController
{
    protected String $userModel = Teacher::class;
    protected String $formRequestValidation = InviteTeachersRequest::class;

    public function store(
        InviteTeachersRequest $request,
        InviteUserService $invite_user_service,  
    ) {
        return $this->handleStore(
            $request->input(),
            $invite_user_service,
        );  
    }
}
