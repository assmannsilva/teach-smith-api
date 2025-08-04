<?php

use App\Models\Organization;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\User\AuthService;
use App\Services\User\UserService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('registers invited user through standard-auth controller endpoint', function() {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'organization_id' => $organization->id,
        'active' => false
    ]);
    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service->shouldReceive('registerInvitedUser')->andReturn(User::factory()->create(['active' => true]));
    $this->app->instance(AuthService::class, $auth_service);

    $response = $this->postJson(route('auth.register.invited'),[
        'user_id' => $user->id,
        'password' => 'senhateste@123',
        'password_confirmation' => 'senhateste@123'
    ]);
    $response->assertStatus(201);
    $response->assertJsonStructure(['user' => ['id', 'first_name', 'surname']]);
    $response->assertJsonFragment(['message' => 'User registered successfully']);
});

it('cannot register invited user through standard-auth controller endpoint due to invalid data', function() {
    $response = $this->postJson(route('auth.register.invited'),[
        'user_id' => '',
        'password' => 'senhateste',
        'password_confirmation' => 'teste'
    ]);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['user_id', 'password']);
});

it('cannot register invited user through standard-auth controller endpoint due to user already registered', function() {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'organization_id' => $organization->id,
        'active' => false
    ]);
    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service
    ->shouldReceive('registerInvitedUser')
    ->andThrow(new \App\Exceptions\UserAlreadyRegisteredException($user));
    $this->app->instance(AuthService::class, $auth_service);

    $response = $this->postJson(route('auth.register.invited'),[
        'user_id' => $user->id,
        'password' => 'senhateste@123',
        'password_confirmation' => 'senhateste@123'
    ]);
    $response->assertStatus(409);
    $response->assertJson([
        'message' =>  "User with email {$user->email} is already registered."
    ]);
});
