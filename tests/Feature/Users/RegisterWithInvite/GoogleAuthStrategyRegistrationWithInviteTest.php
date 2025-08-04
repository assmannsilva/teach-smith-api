<?php

use App\Enums\ProvidersEnum;
use App\Exceptions\InvalidStateRequestException;
use App\Exceptions\InvalidTokenException;
use App\Exceptions\UserAlreadyRegisteredException;
use App\Lib\AuthStrategy\GoogleAuthStrategy;
use App\Services\User\AuthService;
use App\Models\User;
use Illuminate\Support\Facades\Session;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function() {
    $this->userInfo = new \Google\Service\Oauth2\Userinfo();
    $this->userInfo->id = 'google-id-456';
    $this->userInfo->email = "invite@gmail.com";
    $this->userInfo->givenName = "Invited";
    $this->userInfo->familyName = "User";

    $this->repository = app(\App\Repositories\Interfaces\UserRepositoryInterface::class);
    $this->organization = \App\Models\Organization::factory()->create();
    $this->invitedUser = User::factory()->create([
        'first_name' => 'Test',
        'surname' => 'Test',
        'email' => 'test@outlook.com',
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

it('registers an invited user via GoogleAuth', function() {
    $clientMock = Mockery::mock(\Google\Client::class);
    $clientMock->shouldReceive('setRedirectUri');
    $clientMock->shouldReceive('fetchAccessTokenWithAuthCode')->andReturn(['access_token' => 'valid-token']);
    $clientMock->shouldReceive('setAccessToken');

    $strategy = Mockery::mock(GoogleAuthStrategy::class, [$clientMock, $this->repository])->makePartial();
    $strategy->shouldAllowMockingProtectedMethods();
    $strategy->shouldReceive('getUserInfo')->andReturn($this->userInfo);

    $authService = new AuthService(app(\App\Services\User\UserService::class));

    /** @var \App\Lib\AuthStrategy\Interfaces\AuthStrategyInterface&\Mockery\LegacyMockInterface $strategy */
    $user = $authService->registerInvitedUser($this->invitedUser, 'valid-token',$this->state, $strategy);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->email)->toBe($this->userInfo->email)
        ->and($user->active)->toBeTrue();
});

it('cannot register invited user with invalid token', function() {
    $clientMock = Mockery::mock(\Google\Client::class);
    $clientMock->shouldReceive('setRedirectUri');
    $clientMock->shouldReceive('fetchAccessTokenWithAuthCode')->andReturn(['error_description' => 'error']);
    $clientMock->shouldReceive('setAccessToken');

    $strategy = Mockery::mock(GoogleAuthStrategy::class, [$clientMock, $this->repository])->makePartial();
    $authService = new AuthService(app(\App\Services\User\UserService::class));

    $this->expectException(InvalidTokenException::class);
    $this->expectExceptionMessage("error");

    /** @var \App\Lib\AuthStrategy\Interfaces\AuthStrategyInterface&\Mockery\LegacyMockInterface $strategy */
    $authService->registerInvitedUser($this->invitedUser, 'invalid-token',$this->state, $strategy);
});

it('cannot register invited user with invalid state', function() {
    $clientMock = Mockery::mock(\Google\Client::class);
    $strategy = Mockery::mock(GoogleAuthStrategy::class, [$clientMock, $this->repository])->makePartial();
    $authService = new AuthService(app(\App\Services\User\UserService::class));

    $strategy->shouldAllowMockingProtectedMethods();
    $strategy->shouldReceive('checkState')->andReturn(false);

    $this->expectException(InvalidStateRequestException::class);

    /** @var \App\Lib\AuthStrategy\Interfaces\AuthStrategyInterface&\Mockery\LegacyMockInterface $strategy */
    $authService->registerInvitedUser($this->invitedUser, 'valid-token','invalid-state', $strategy);
});

it('cannot register invited user already registered', function() {
    $clientMock = Mockery::mock(\Google\Client::class);
    $clientMock->shouldReceive('setRedirectUri');
    $clientMock->shouldReceive('fetchAccessTokenWithAuthCode')->andReturn(['access_token' => 'valid-token']);
    $clientMock->shouldReceive('setAccessToken');

    $strategy = Mockery::mock(GoogleAuthStrategy::class, [$clientMock, $this->repository])->makePartial();
    $strategy->shouldAllowMockingProtectedMethods();
    $strategy->shouldReceive('getUserInfo')->andReturn($this->userInfo);

    $authService = new AuthService(app(\App\Services\User\UserService::class));

    User::factory()->create([
        'email' => $this->userInfo->email,
        'provider_id' => $this->userInfo->id,
        'provider' => ProvidersEnum::GOOGLE,
        'active' => true,
    ]);

    $this->expectException(UserAlreadyRegisteredException::class);

    /** @var \App\Lib\AuthStrategy\Interfaces\AuthStrategyInterface&\Mockery\LegacyMockInterface $strategy */
    $authService->registerInvitedUser($this->invitedUser, 'valid-token',$this->state, $strategy);
});
