<?php

namespace App\Lib\AuthStrategy\Traits;

use Illuminate\Support\Str;

trait HasState
{
    protected string|null $state;

    /**
     * Stores the CSRF token in the session.
     * @return void
     */
    private function storeCsrfToken(string $csrf): void
    {
        session()->put("csrf_state_token",$csrf);
    }
    
    /**
     * Generates a random state for authentication.
     *
     * @return string
     */
    public function generateState(array $extras = []): string
    {
        $csrf = Str::random(40);
        $this->state = base64_encode(\json_encode([
            ...$extras,
            "csrf" => $csrf
        ]));

        $this->storeCsrfToken($csrf);
        return $this->state;
    }

    /**
     * Checks if the provided state matches the stored CSRF token.
     * @param string $state
     * @return bool
     */
    public function checkState(?string $state) : bool
    {
        if(!$state || !\is_string($state)) return false;
        
        $state_decoded = \json_decode(\base64_decode($state),\true);
        $session_state = session()->get("csrf_state_token");
        session()->forget("csrf_state_token");
        if (!$session_state || !isset($state_decoded["csrf"])) return false;
        
        return hash_equals($session_state, $state_decoded["csrf"]);
    }
}