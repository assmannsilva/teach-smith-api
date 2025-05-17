<?php
namespace App\Lib\LoginStrategy\Interfaces;

use App\Models\User;

interface ExternalProviderInterface extends AuthStrategyInterface
{
    public function generateOAuthUrl() : string;

    public function generateToken(string $code) : string;
}