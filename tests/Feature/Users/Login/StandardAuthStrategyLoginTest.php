<?php

use App\Exceptions\UserNotFoundException;
use App\Lib\AuthStrategy\StandardAuthStrategy;
use App\Services\User\AuthService;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function() {
    $repository = app(\App\Repositories\Interfaces\UserRepositoryInterface::class);
    $this->strategy = app(StandardAuthStrategy::class, ['userRepository' => $repository]);
});

it('login an user using Standard Auth Strategy', function() {
    User::factory()->create([
        'email' => 'teste@gmail.com',
        'password' => bcrypt('teste@123'),
    ]);

    $authService = new AuthService(app(\App\Services\User\UserService::class));

    $authenticated = $authService->authenticate([
        'email' => 'teste@gmail.com',
        'password' => 'teste@123',
    ],$this->strategy);

    expect($authenticated)->toBeTrue(true);
});

it('cannot login an user that not has the specified email', function() {
    $authService = new AuthService(app(\App\Services\User\UserService::class));

    $this->expectException(UserNotFoundException::class);

    $authService->authenticate([
        'email' => 'teste@gmail.com',
        'password' => 'teste@123',
    ],$this->strategy);
});

it('cannot login an user with wrong password specified', function() {
        User::factory()->create([
        'email' => 'teste@gmail.com',
        'password' => bcrypt('teste@1234'),
    ]);
    $authService = new AuthService(app(\App\Services\User\UserService::class));

    $authenticated = $authService->authenticate([
        'email' => 'teste@gmail.com',
        'password' => 'teste@123',
    ],$this->strategy);

    expect($authenticated)->toBe(false);

});
