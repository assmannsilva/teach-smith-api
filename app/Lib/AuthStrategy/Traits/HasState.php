<?php

namespace App\Lib\AuthStrategy\Traits;

use Psy\Util\Str;

trait HasState
{
    protected string|null $state;

    /**
     * armazena o estado
     * @return void
     */
    private function storeState(): void
    {
        session()->put("state",$this->state);
    }
    
    /**
     * Gera um estado aleatório para a autenticação
     *
     * @return string
     */
    public function generateState(): string
    {
        $this->state = Str::random(40);
        $this->storeState();
        return $this->state;
    }

    public function checkState(string $state) : bool
    {
        $session_state = session()->get("state");
        if (!$session_state) return false;
        
        return hash_equals($session_state, $state);
    }




}