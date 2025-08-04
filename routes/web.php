<?php

use App\Http\Controllers\Users\Auth\GoogleAuthController;
use App\Http\Controllers\Users\Auth\StandardAuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response("OK");
});

Route::prefix('google-auth')->controller(GoogleAuthController::class)->group(function () {
    Route::get('/generate-login-url', 'generateOAuthLoginUrl')->name('google.auth.login');
    Route::get('/generate-register-url', 'generateOAuthRegisterUrl')->name('google.auth.register');
    Route::get('/generate-regiter-invited-url', 'generateOAuthInvitedUrl')->name('google.auth.register.invited');

    Route::get('/login', 'authenticateCallback')->name('google.auth.login.callback');
    Route::get('/register', 'registerCallback')->name('google.auth.register.callback');
    Route::get('/register-invited', 'registerInvitedUserCallback')->name('google.auth.register.invited.callback');
});

Route::prefix('standard-auth')->controller(StandardAuthController::class)->group(function () {
    Route::post('/register', 'register')->name('auth.register');
    Route::post('/register-invited', 'registerWithInvite')->name('auth.register.invited');
    Route::post('/login', 'authenticate')->name('auth.login');
});

Route::middleware("auth:sanctum")->get('/organization_logo', function () {
    $path = Auth::user()->organization->logo_url;
    if (!file_exists($path)) abort(404);
    return response()->file($path);
});