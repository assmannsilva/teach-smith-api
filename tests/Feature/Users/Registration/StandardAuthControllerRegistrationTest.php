<?php

use App\Models\Organization;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\User\AuthService;
use App\Services\User\UserService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('registers user through standard-auth controller endpoint', function() {
    $organization = Organization::factory()->create();
    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service->shouldReceive('register')->andReturn(User::factory()->create());
    $this->app->instance(AuthService::class, $auth_service);

    $response = $this->postJson(route('auth.register'),[
        'first_name' => 'Teste',
        'email' => 'teste@gmail.com',
        'surname' => 'Usuário',
        'organization_id' => $organization->id,
        "password" => 'senhateste@123',
        "password_confirmation" => 'senhateste@123'
    ]);
    $response->assertStatus(201);
    $response->assertJsonStructure(['user' => ['id', 'first_name', 'surname']]);
    $response->assertJsonFragment(['message' => 'User registered successfully']);
});

it('cannot register user through standard-auth controller endpoint due to invalid data', function() {
    $response = $this->postJson(route('auth.register'),[
        'first_name' => 'Teste',
        'email' => "teste",
        'surname' => 'Usuário',
        'organization_id' => 1,
        "password" => 'senhateste',
        "password_confirmation" => 'teste'
    ]);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email', 'password', 'organization_id']);
});

it('cannot register user through standard-auth controller endpoint due to user already registered', function() {
    $organization = Organization::factory()->create();
    $user = User::factory()->create(["email" => "teste@gmail.com"]);

    $auth_service = Mockery::mock(AuthService::class, [new UserService(new UserRepository)]);
    $auth_service
    ->shouldReceive('register')
    ->andThrow(new \App\Exceptions\UserAlreadyRegisteredException($user));
    $this->app->instance(AuthService::class, $auth_service);

        $response = $this->postJson(route('auth.register'),[
        'first_name' => 'Teste',
        'email' => 'teste@gmail.com',
        'surname' => 'Usuário',
        'organization_id' => $organization->id,
        "password" => 'senhateste@123',
        "password_confirmation" => 'senhateste@123'
    ]);
    
    $response->assertStatus(409);
    $response->assertJson([
        'message' =>  "User with email {$user->email} is already registered."
    ]);
});

