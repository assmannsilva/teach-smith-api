<?php
namespace App\Lib\AuthStrategy\Interfaces;

use App\Enums\ProvidersActionsEnum;

interface ExternalProviderInterface extends AuthStrategyInterface
{
    public function generateOAuthUrl(ProvidersActionsEnum $action, array $extras = []) : string;

    public function generateToken(string $code) : string;
}