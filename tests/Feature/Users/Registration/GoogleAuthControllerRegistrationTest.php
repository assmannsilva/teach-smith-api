<?php

use App\Models\Organization;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\User\AuthService;
use App\Services\User\UserService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function() {
});

it('registers user through google auth endpoint', function() {

    $organization = Organization::factory()->create();
    $csrf = Str::random(40);
    $state = base64_encode(json_encode([
        "organization_id" => $organization->id,
        "csrf" => $csrf
    ]));

    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service->shouldReceive('register')->andReturn(User::factory()->create());
    $this->app->instance(AuthService::class, $auth_service);

    $response = $this->get(route('google.auth.register.callback', ['state' => $state, 'code' => 'valid-code']));
    $response->assertStatus(201);
    $response->assertJsonStructure(['user' => ['id', 'first_name', 'surname']]);
    $response->assertJsonFragment(['message' => 'User registered successfully']);
});


it('cannot register via google due to invalid token', function() {

    $organization = Organization::factory()->create();
    $state = base64_encode(json_encode([
        "organization_id" => $organization->id,
        "csrf" => "invalid-csrf-token"
    ]));

    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service->shouldReceive('register')->andThrow(new \App\Exceptions\InvalidTokenException('Invalid token'));
    $this->app->instance(AuthService::class, $auth_service);

    $response = $this->get(route('google.auth.register.callback', ['state' => $state, 'code' => 'valid-code']));
    
    $response->assertStatus(400);
    $response->assertJson([
        'message' => 'Invalid token'
    ]);
});

it('cannot register via google due to invalid state', function() {

    $organization = Organization::factory()->create();
    $state = base64_encode(json_encode([
        "organization_id" => $organization->id,
        "csrf" => "invalid-csrf-token"
    ]));

    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service->shouldReceive('register')->andThrow(new \App\Exceptions\InvalidStateRequestException());
    $this->app->instance(AuthService::class, $auth_service);

    $response = $this->get(route('google.auth.register.callback', ['state' => $state, 'code' => 'valid-code']));
    
    $response->assertStatus(400);
    $response->assertJson([
        'message' => 'Invalid state request.'
    ]);
});

it('cannot register via google due to user already registered', function() {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();
    $state = base64_encode(json_encode([
        "organization_id" => $organization->id,
        "csrf" => "invalid-csrf-token"
    ]));

    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service
    ->shouldReceive('register')
    ->andThrow(new \App\Exceptions\UserAlreadyRegisteredException($user));
    $this->app->instance(AuthService::class, $auth_service);

    $response = $this->get(route('google.auth.register.callback', ['state' => $state, 'code' => 'valid-code']));
    
    $response->assertStatus(409);
    $response->assertJson([
        'message' =>  "User with email {$user->email} is already registered."
    ]);
});
