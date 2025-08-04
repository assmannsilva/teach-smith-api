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

    /**
     * Generate the OAuth login URL for Google authentication.
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateOAuthLoginUrl()
    {
        return \response()->json([
            'url' => $this->authService->generateOAuthUrl(ProvidersActionsEnum::LOGIN,$this->strategy)
        ]);
    }

    /**
     * Generate the OAuth register URL for Google authentication.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateOAuthRegisterUrl(Request $request)
    {
        return \response()->json([
            'url' => $this->authService->generateOAuthUrl(ProvidersActionsEnum::REGISTER,$this->strategy,[
                "organization_id" => $request->input("organization_id")
            ])
        ]);
    }

    /**
     * Generate the OAuth register URL for invited users.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateOAuthInvitedUrl(Request $request)
    {
        return \response()->json([
            'url' => $this->authService->generateOAuthUrl(ProvidersActionsEnum::REGISTER,$this->strategy,[
                "user_id" => $request->input("user_id")
            ])
        ]);
    }

    /**
     * Handle the callback from Google after authentication.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticateCallback(Request $request) 
    {
       $this->authService->authenticate($request->input(),$this->strategy);
       return \redirect()->to(\config("app.front_url") . "/home");
    }

    /**
     * Handle the callback for user registration after Google authentication.
     * @param Request $request
     * @param OrganizationService $organization_service
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerCallback(
        Request $request,
        OrganizationService $organization_service
    ) {
        $organization = $organization_service->findByCriptedState($request->input("state"));
        $this->authService->register([
                "organization_id" => $organization->id
            ],
            $request->input("code"),
            $request->input("state"),
            $this->strategy
        );
        
        return \redirect()->to(\config("app.front_url") . "/home");
    }

    /**
     * Handle the callback for invited user registration after Google authentication.
     * @param Request $request
     * @param UserService $user_service
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerInvitedUserCallback(
        Request $request,
        UserService $user_service
    ) {
        $user = $user_service->findByCriptedState($request->input("state"));
        $user = $this->authService->registerInvitedUser(
            $user,
            $request->input("code"),
            $request->input("state"),
            $this->strategy
        );
        return \response()->json([
            "message" => "User registered successfully",
            "user" => $user
        ],201);
    }
}
