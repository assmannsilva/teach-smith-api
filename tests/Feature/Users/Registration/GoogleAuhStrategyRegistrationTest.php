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
    $this->userInfo->id = 'google-id-123';
    $this->userInfo->email = "teste@gmail.com";
    $this->userInfo->givenName = "Test";
    $this->userInfo->familyName = "User";

    $this->repository = app(\App\Repositories\Interfaces\UserRepositoryInterface::class);
    $this->organization = \App\Models\Organization::factory()->create();
    
    Session::start();
    $csrf = Str::random(40);
    $this->state = base64_encode(\json_encode([
        "organization_id" => $this->organization->id,
        "csrf" => $csrf
    ]));

    Session::put("csrf_state_token",$csrf);
});

it('registers a new user via GoogleAuth', function() {
    $clientMock = Mockery::mock(\Google\Client::class);
    $clientMock->shouldReceive('setRedirectUri');
    $clientMock->shouldReceive('fetchAccessTokenWithAuthCode')->andReturn(['access_token' => 'valid-token']);
    $clientMock->shouldReceive('setAccessToken');

    $strategy = Mockery::mock(GoogleAuthStrategy::class, [$clientMock, $this->repository])->makePartial();
    $strategy->shouldAllowMockingProtectedMethods();
    $strategy->shouldReceive('getUserInfo')->andReturn($this->userInfo);

    $authService = new AuthService(app(\App\Services\User\UserService::class));

    /** @var \App\Lib\AuthStrategy\Interfaces\AuthStrategyInterface&\Mockery\LegacyMockInterface $strategy */
    $user = $authService->register(null, 'valid-token',$this->state, $strategy);

    expect($user)->toBeInstanceOf(User::class)->and($user->email)->toBe($this->userInfo->email);
});

it("cannot register a user with invalid token", function() {
    $clientMock = Mockery::mock(\Google\Client::class);
    $clientMock->shouldReceive('setRedirectUri');
    $clientMock->shouldReceive('fetchAccessTokenWithAuthCode')->andReturn(['error_description' => 'error']);
    $clientMock->shouldReceive('setAccessToken');

    $strategy = Mockery::mock(GoogleAuthStrategy::class, [$clientMock, $this->repository])->makePartial();
    $authService = new AuthService(app(\App\Services\User\UserService::class));

    $this->expectException(InvalidTokenException::class);
    $this->expectExceptionMessage("error");

    /** @var \App\Lib\AuthStrategy\Interfaces\AuthStrategyInterface&\Mockery\LegacyMockInterface $strategy */
    $authService->register(null, 'invalid-token', $this->state, $strategy);
});

it("cannot register a user with invalid state", function() {
    $clientMock = Mockery::mock(\Google\Client::class);
    $strategy = Mockery::mock(GoogleAuthStrategy::class, [$clientMock, $this->repository])->makePartial();
    $authService = new AuthService(app(\App\Services\User\UserService::class));

    $this->expectException(InvalidStateRequestException::class);

    /** @var \App\Lib\AuthStrategy\Interfaces\AuthStrategyInterface&\Mockery\LegacyMockInterface $strategy */
    $authService->register(null, 'valid-token', "invalid-state", $strategy);
});

it('cannot register a user already registered', function() {
    User::factory()->create([
        'email' => $this->userInfo->email,
        'provider_id' => $this->userInfo->id,
        "provider" => ProvidersEnum::GOOGLE
    ]);

    $clientMock = Mockery::mock(\Google\Client::class);
    $clientMock->shouldReceive('setRedirectUri');
    $clientMock->shouldReceive('fetchAccessTokenWithAuthCode')->andReturn(['access_token' => 'valid-token']);
    $clientMock->shouldReceive('setAccessToken');

    $strategy = Mockery::mock(GoogleAuthStrategy::class, [$clientMock, $this->repository])->makePartial();
    $strategy->shouldAllowMockingProtectedMethods();
    $strategy->shouldReceive('getUserInfo')->andReturn($this->userInfo);

    $authService = new AuthService(app(\App\Services\User\UserService::class));

    $this->expectException(UserAlreadyRegisteredException::class);

    /** @var \App\Lib\AuthStrategy\Interfaces\AuthStrategyInterface&\Mockery\LegacyMockInterface $strategy */
    $authService->register(null, 'valid-token',$this->state, $strategy);
});
