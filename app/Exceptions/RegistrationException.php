<?php

namespace App\Exceptions;

use Exception;

class RegistrationException extends Exception
{
    /**
     * Check if the exception is related to the Google authentication flow.
     * If so, it redirects to the appropriate URL based on the route name.
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function checkGoogleAuthFlowException($request)
    {
        $routeName = $request->route()?->getName();
        if (\Str::startsWith($routeName, 'google.auth.')) {
            return match ($routeName) {
                'google.auth.login.callback' => \config("app.front_url")."/",
                'google.auth.register.callback' => \config("app.front_url")."/register",
                'google.auth.register.invited.callback' => \config("app.front_url")."/",
                default => \config("app.front_url")."/"
            };
        }
        return null;
        
    }

    public function render($request)
    {
        if($redirect = $this->checkGoogleAuthFlowException($request)) {
            return redirect()->to($redirect . '?error=' . urlencode($this->getMessage()));
        }

        return response()->json([
            'message' => $this->getMessage()
        ], $this->code);
    }
}
