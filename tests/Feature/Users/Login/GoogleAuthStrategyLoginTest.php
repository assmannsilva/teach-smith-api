<?php

use App\Enums\ProvidersEnum;
use App\Exceptions\InvalidStateRequestException;
use App\Exceptions\InvalidTokenException;
use App\Exceptions\UserNotFoundException;
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

it('login via GoogleAuth', function() {
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

    /** @var \App\Lib\AuthStrategy\Interfaces\AuthStrategyInterface&\Mockery\LegacyMockInterface $strategy */
    $autenthicated = $authService->authenticate(['code' => 'valid-code', 'state' => $this->state], $strategy);

    expect($autenthicated)->toBeTrue();
});

it("cannot login a user via GoogleAuth with invalid code", function() {
    $clientMock = Mockery::mock(\Google\Client::class);
    $clientMock->shouldReceive('setRedirectUri');
    $clientMock->shouldReceive('fetchAccessTokenWithAuthCode')->andReturn(['error_description' => 'error']);
    $clientMock->shouldReceive('setAccessToken');

    $strategy = Mockery::mock(GoogleAuthStrategy::class, [$clientMock, $this->repository])->makePartial();
    $authService = new AuthService(app(\App\Services\User\UserService::class));

    $this->expectException(InvalidTokenException::class);
    $this->expectExceptionMessage("error");

    /** @var \App\Lib\AuthStrategy\Interfaces\AuthStrategyInterface&\Mockery\LegacyMockInterface $strategy */
    $autenthicated = $authService->authenticate(['code' => 'valid-code', 'state' => $this->state], $strategy);

    expect($autenthicated)->toBe(false);
});

it("cannot login a user via GoogleAuth with invalid state", function() {
    $clientMock = Mockery::mock(\Google\Client::class);
    $strategy = Mockery::mock(GoogleAuthStrategy::class, [$clientMock, $this->repository])->makePartial();
    $authService = new AuthService(app(\App\Services\User\UserService::class));

    $this->expectException(InvalidStateRequestException::class);

    /** @var \App\Lib\AuthStrategy\Interfaces\AuthStrategyInterface&\Mockery\LegacyMockInterface $strategy */
    $autenthicated = $authService->authenticate(['code' => 'valid-code', 'state' => "invalid-state"], $strategy);
    expect($autenthicated)->toBe(false);
});

it('cannot login a user via GoogleAuth that not exists', function() {

    $clientMock = Mockery::mock(\Google\Client::class);
    $clientMock->shouldReceive('setRedirectUri');
    $clientMock->shouldReceive('fetchAccessTokenWithAuthCode')->andReturn(['access_token' => 'valid-token']);
    $clientMock->shouldReceive('setAccessToken');

    $strategy = Mockery::mock(GoogleAuthStrategy::class, [$clientMock, $this->repository])->makePartial();
    $strategy->shouldAllowMockingProtectedMethods();
    $strategy->shouldReceive('getUserInfo')->andReturn($this->userInfo);

    $authService = new AuthService(app(\App\Services\User\UserService::class));

    $this->expectException(UserNotFoundException::class);
    /** @var \App\Lib\AuthStrategy\Interfaces\AuthStrategyInterface&\Mockery\LegacyMockInterface $strategy */
    $autenthicated = $authService->authenticate(['code' => 'valid-code', 'state' => $this->state], $strategy);
});
