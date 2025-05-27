<?php

namespace App\Lib\AuthStrategy\Traits;

use Illuminate\Support\Str;

trait HasState
{
    protected string|null $state;

    /**
     * armazena o estado
     * @return void
     */
    private function storeCsrfToken(string $csrf): void
    {
        session()->put("csrf_state_token",$csrf);
    }
    
    /**
     * Gera um estado aleatório para a autenticação
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

    public function checkState(string $state) : bool
    {
        $state_decoded = \json_decode(\base64_decode($state),\true);
        $session_state = session()->get("csrf_state_token");
        session()->forget("csrf_state_token");
        if (!$session_state || !isset($state_decoded["csrf"])) return false;
        
        return hash_equals($session_state, $state_decoded["csrf"]);
    }
    




}