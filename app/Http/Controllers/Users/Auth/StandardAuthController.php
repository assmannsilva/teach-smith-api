<?php

namespace App\Http\Controllers\Users\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StandardLoginRequest;
use App\Http\Requests\Users\StandardRegistrationRequest;
use App\Lib\AuthStrategy\StandardAuthStrategy;
use App\Services\User\AuthService;

class StandardAuthController extends Controller
{
    public function __construct(
        private StandardAuthStrategy $strategy,
        private AuthService $authService,
    ) { }
    
    /**
     * Generate the OAuth login URL for standard authentication.
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(StandardLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $authenticated = $this->authService->authenticate($credentials,$this->strategy);

        return \response()->json([
            "message" => $authenticated ? "User authenticated successfully" : "Incorrect Credentials" 
        ],$authenticated ? 200 : 403);
    }

    /**
     * Handle user registration with standard authentication.
     * @param StandardRegistrationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(StandardRegistrationRequest $request)
    {
        $credential = $request->input("password");
        $registration_data = $request->except("password");
        $user_registrated = $this->authService->register(
            $registration_data,
            $credential,
            null,
            $this->strategy
        );

        return \response()->json([
            "message" => "User registered successfully",
            "user" => $user_registrated
        ],201);
    }
}