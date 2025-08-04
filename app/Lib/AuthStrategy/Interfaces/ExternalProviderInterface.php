<?php
namespace App\Lib\AuthStrategy\Interfaces;

use App\Enums\ProvidersActionsEnum;

interface ExternalProviderInterface extends AuthStrategyInterface
{
    /**
     * Generates an OAuth URL for the specified action and extras.
     * @param ProvidersActionsEnum $action
     * @param array $extras
     * @return string
     */
    public function generateOAuthUrl(ProvidersActionsEnum $action, array $extras = []) : string;
    
    /**
     * Generates a token based on the provided code.
     * @param string $code
     * @return string
     */
    public function generateToken(string $code) : string;
}