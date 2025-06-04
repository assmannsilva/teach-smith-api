<?php

use App\Lib\AuthStrategy\GoogleAuthStrategy;
use Google\Client;
use App\Enums\ProvidersActionsEnum;

uses()->group('unit');

it('generates correct Google OAuth URL', function () {
    $oauthServiceMock = Mockery::mock();
    $oauthServiceMock
        ->shouldReceive('generateCodeVerifier')
        ->once()
        ->andReturn('fake-code-verifier');

    $clientMock = Mockery::mock(Client::class);
    $clientMock->shouldReceive('getOAuth2Service')->andReturn($oauthServiceMock);
    $clientMock->shouldReceive('setRedirectUri')->once();
    $clientMock->shouldReceive('addScope')->times(2);
    $clientMock->shouldReceive('setPrompt')->once();
    $clientMock->shouldReceive('setState')->once();
    $clientMock->shouldReceive('createAuthUrl')->andReturn('https://accounts.google.com/oauth2/auth');

    $userRepoMock = Mockery::mock(\App\Repositories\Interfaces\UserRepositoryInterface::class);

    $strategy = new GoogleAuthStrategy($clientMock, $userRepoMock);

    $url = $strategy->generateOAuthUrl(ProvidersActionsEnum::LOGIN);

    expect($url)->toBe('https://accounts.google.com/oauth2/auth');
    expect(session('google_code_verifier'))->toBe('fake-code-verifier');
});

afterEach(fn () => Mockery::close());
