<?php

use App\Http\Controllers\Users\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response("OK");
});

Route::prefix('google-auth')->controller(GoogleAuthController::class)->group(function () {
    Route::get('/generate-login-url', 'generateOAuthLoginUrl');
    Route::get('/generate-register-url', 'generateOAuthRegisterUrl');
    Route::get('/generate-regiter-invited-url', 'generateOAuthInvitedUrl');

    Route::get('/login', 'authenticateCallback');
    Route::get('/register', 'registerCallback');
    Route::get('/register-invited', 'registerInvitedUserCallback');
});
