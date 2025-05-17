<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\RegisterAdminUserRequest;
use App\Services\User\UserService;

class RegisterAdminUserController extends Controller
{
    public function __invoke(
        RegisterAdminUserRequest $request,
        UserService $user_service
    ){
        $user = $user_service->registerAdminUser($request->input());
        return \response()->json([
            'user' => $user
        ],201);
    }
}
