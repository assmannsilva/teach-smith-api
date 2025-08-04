<?php

use App\Exceptions\UserAlreadyRegisteredException;
use App\Lib\AuthStrategy\StandardAuthStrategy;
use App\Services\User\AuthService;
use App\Models\User;
use Illuminate\Support\Facades\Session;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function() {
    $repository = app(\App\Repositories\Interfaces\UserRepositoryInterface::class);
    $this->strategy = app(StandardAuthStrategy::class, [
        'userRepository' => $repository
    ]);
    $this->organization = \App\Models\Organization::factory()->create();
    $this->invitedUser = User::factory()->create([
        'first_name' => 'Test',
        'surname' => 'Test',
        'email' => 'invite@outlook.com',
        'provider_id' => null,
        'provider' => null,
        'organization_id' => $this->organization->id,
        'active' => false,
    ]);
    Session::start();
    $csrf = Str::random(40);
    $this->state = base64_encode(json_encode([
        "user_id" => $this->invitedUser->id,
        "csrf" => $csrf
    ]));
    Session::put("csrf_state_token", $csrf);
});

it('registers an invited user using Standard Auth Strategy', function() {
    $authService = new AuthService(app(\App\Services\User\UserService::class));

    $user = $authService->registerInvitedUser(
        $this->invitedUser,
        'senhateste@123',
        null,
        $this->strategy
    );

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->email)->toBe($this->invitedUser->email)
        ->and($user->active)->toBeTrue();
});

it('cannot register an invited user already registered', function() {
    $authService = new AuthService(app(\App\Services\User\UserService::class));

    User::factory()->create([
        'email' => $this->invitedUser->email,
        'provider_id' => null,
        'provider' => null,
        'organization_id' => $this->organization->id,
        'active' => true,
    ]);

    $this->expectException(UserAlreadyRegisteredException::class);

    $authService->registerInvitedUser(
        $this->invitedUser,
        'senhateste@123',
        null,
        $this->strategy
    );
});
