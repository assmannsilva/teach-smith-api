<?php

namespace App\Providers;

use App\Lib\AuthStrategy\GoogleAuthStrategy;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Google\Client;

class ExternalLoginProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function () {
            $client = new Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setRedirectUri(config('services.google.redirect_uri'));
            return $client;
        });

        $this->app->singleton(GoogleAuthStrategy::class, function ($app) {
            return new GoogleAuthStrategy($app->make(Client::class),$app->make(UserRepositoryInterface::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
