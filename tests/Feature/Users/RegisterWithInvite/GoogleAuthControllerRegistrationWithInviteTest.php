<?php

use App\Exceptions\UserAlreadyRegisteredException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\User\AuthService;
use App\Services\User\UserService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function() {
});

it('registers invited user through google auth endpoint', function() {
    $user = User::factory()->create(['active' => false]);
    $csrf = Str::random(40);
    $state = base64_encode(json_encode([
        "user_id" => $user->id,
        "csrf" => $csrf
    ]));

    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service->shouldReceive('registerInvitedUser')->andReturn(User::factory()->create(['active' => true]));
    $this->app->instance(AuthService::class, $auth_service);

    $response = $this->get(route('google.auth.register.invited.callback', ['state' => $state, 'code' => 'valid-code']));
    $response->assertStatus(201);
    $response->assertJsonFragment(["message" => "User registered successfully"]);
});

it('cannot register invited user via google due to invalid token', function() {
    $user = User::factory()->create(['active' => false]);
    $csrf = Str::random(40);
    $state = base64_encode(json_encode([
        "user_id" => $user->id,
        "csrf" => $csrf
    ]));

    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service->shouldReceive('registerInvitedUser')->andThrow(new \App\Exceptions\InvalidTokenException('Invalid token'));
    $this->app->instance(AuthService::class, $auth_service);

    $response = $this->get(route('google.auth.register.invited.callback', ['state' => $state, 'code' => 'valid-code']));
    $response->assertStatus(302);
    $response->assertRedirect(\config('app.front_url') . '/complete-registration?error=' . urlencode('Invalid token'));
});

it('cannot register invited user via google due to invalid state', function() {
    $user = User::factory()->create(['active' => false]);
    $csrf = Str::random(40);
    $state = base64_encode(json_encode([
        "user_id" => $user->id,
        "csrf" => $csrf
    ]));

    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service->shouldReceive('registerInvitedUser')->andThrow(new \App\Exceptions\InvalidStateRequestException());
    $this->app->instance(AuthService::class, $auth_service);

    $response = $this->get(route('google.auth.register.invited.callback', ['state' => $state, 'code' => 'valid-code']));
    $response->assertStatus(302);
    $response->assertRedirect(\config('app.front_url') . '/complete-registration?error=' . urlencode('Invalid token'));
});

it('cannot register invited user via google due to user already registered', function() {
    $user = User::factory()->create(['active' => false]);
    $csrf = Str::random(40);
    $state = base64_encode(json_encode([
        "user_id" => $user->id,
        "csrf" => $csrf
    ]));

    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service->shouldReceive('registerInvitedUser')->andThrow(new UserAlreadyRegisteredException($user));
    $this->app->instance(AuthService::class, $auth_service);

    $response = $this->get(route('google.auth.register.invited.callback', ['state' => $state, 'code' => 'valid-code']));
    $response->assertStatus(302);
    $response->assertRedirect(\config('app.front_url') . '/complete-registration?error=' . urlencode("User with email {$user->email} is already registered."));
});
