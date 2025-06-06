<?php

use App\Models\Organization;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\User\AuthService;
use App\Services\User\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('login user through google auth endpoint', function() {
    $state = base64_encode(json_encode(["csrf" => "valid"]));

    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service->shouldReceive('authenticate')->andReturn(true);
    $this->app->instance(AuthService::class, $auth_service);

    $response = $this->get(route('google.auth.login.callback', ['state' => $state, 'code' => 'valid-code']));
    $response->assertStatus(200);
    $response->assertJsonFragment(['message' => 'User authenticated successfully']);
});


it('cannot login via google due to invalid token', function() {
     $state = base64_encode(json_encode(["csrf" => "valid"]));

    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service->shouldReceive('authenticate')->andThrow(new \App\Exceptions\InvalidTokenException('Invalid token'));
    $this->app->instance(AuthService::class, $auth_service);

    $response = $this->get(route('google.auth.login.callback', ['state' => $state, 'code' => 'invalid-code']));
    
    $response->assertStatus(400);
    $response->assertJson([
        'error' => 'Invalid token'
    ]);
});

it('cannot login via google due to invalid state', function() {
    $state = base64_encode(json_encode(["csrf" => "invalid"]));
    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service->shouldReceive('authenticate')->andThrow(new \App\Exceptions\InvalidStateRequestException());
    $this->app->instance(AuthService::class, $auth_service);

    $response = $this->get(route('google.auth.login.callback', ['state' => $state, 'code' => 'valid-code']));
    
    $response->assertStatus(400);
    $response->assertJson([
        'error' => 'Invalid state request.'
    ]);
});

it('cannot login via google due to user not exists', function() {
     $state = base64_encode(json_encode(["csrf" => "valid"]));

    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service
    ->shouldReceive('authenticate')
    ->andThrow(new ModelNotFoundException());

    $this->app->instance(AuthService::class, $auth_service);

    $response = $this->getJson(route('google.auth.login.callback', ['state' => $state, 'code' => 'valid-code']));
    
    $response->assertStatus(404);
    $response->assertJson([
        'error' =>  "Not Found"
    ]);
});
