<?php

namespace App\Http\Controllers\Users\Auth;

use App\Enums\ProvidersActionsEnum;
use App\Http\Controllers\Controller;
use App\Lib\AuthStrategy\GoogleAuthStrategy;
use App\Services\OrganizationService;
use App\Services\User\AuthService;
use App\Services\User\UserService;
use Illuminate\Http\Request;

class GoogleAuthController extends Controller
{

    public function __construct(
        private GoogleAuthStrategy $strategy,
        private AuthService $authService,
    ) { }


    public function generateOAuthLoginUrl()
    {
        return \response()->json([
            'url' => $this->authService->generateOAuthUrl(ProvidersActionsEnum::LOGIN,$this->strategy)
        ]);
    }
    
    public function generateOAuthRegisterUrl(Request $request)
    {
        return \response()->json([
            'url' => $this->authService->generateOAuthUrl(ProvidersActionsEnum::REGISTER,$this->strategy,[
                "organization_id" => $request->input("organization_id")
            ])
        ]);
    }

    public function generateOAuthInvitedUrl(Request $request)
    {
        return \response()->json([
            'url' => $this->authService->generateOAuthUrl(ProvidersActionsEnum::REGISTER,$this->strategy,[
                "user_id" => $request->input("user_id")
            ])
        ]);
    }

    public function authenticateCallback(Request $request) 
    {
       $authenticated = $this->authService->authenticate($request->input(),$this->strategy);
       return \response()->json([
            "message" => $authenticated ? "User authenticated successfully" : "Incorrect Credentials" 
        ],$authenticated ? 200 : 403);
    }

    public function registerCallback(
        Request $request,
        OrganizationService $organization_service
    ) {
        $organization = $organization_service->findByCriptedState($request->input("state"));
        $user = $this->authService->register([
                "organization_id" => $organization->id
            ],
            $request->input("code"),
            $request->input("state"),
            $this->strategy
        );
        
        return \response()->json([
            "message" => "User registered successfully",
            "user" => $user
        ],201);
    }

    public function registerInvitedUserCallback(
        Request $request,
        UserService $user_service
    ) {
        $user = $user_service->findByCriptedState($request->input("state"));
        $user = $this->authService->registerInvitedUser($user,$request->input("code"),$this->strategy);
        return \response()->json([
            "message" => "User registered successfully",
            "user" => $user
        ],201);
    }
}
