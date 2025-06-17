<?php

use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepository;
use App\Services\User\AuthService;
use App\Services\User\UserService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('login user through standard-auth controller endpoint', function() {
    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service->shouldReceive('authenticate')->andReturn(true);
    $this->app->instance(AuthService::class, $auth_service);

    $response = $this->postJson(route('auth.login'),['email' => 'teste@gmail.com',"password" => 'senhateste@123']);
    $response->assertStatus(200);
    $response->assertJsonFragment(['message' => "User authenticated successfully"]);
});

it('cannot login user through standard-auth controller endpoint due to wrong credentials', function() {
    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service->shouldReceive('authenticate')->andReturn(false);
    $this->app->instance(AuthService::class, $auth_service);

    $response = $this->postJson(route('auth.login'),['email' => 'teste@gmail.com',"password" => 'wrongpassword']);
    $response->assertStatus(403);
    $response->assertJsonFragment(['message' => "Incorrect Credentials"]);
});

it('cannot login user through standard-auth controller endpoint due to user not exists with this email', function() {

    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service
    ->shouldReceive('authenticate')
    ->andThrow(new UserNotFoundException("User with the given email could not be found."));
    $this->app->instance(AuthService::class, $auth_service);

    $response = $this->postJson(route('auth.login'),[
        'email' => 'teste@gmail.com',
        "password" => 'senhateste@123'
    ]);
    
    $response->assertStatus(404);
    $response->assertJson([
        'error' =>  "User with the given email could not be found."
    ]);
});

