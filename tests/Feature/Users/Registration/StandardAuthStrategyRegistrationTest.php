<?php

use App\Enums\ProvidersEnum;
use App\Exceptions\InvalidStateRequestException;
use App\Exceptions\InvalidTokenException;
use App\Exceptions\UserAlreadyRegisteredException;
use App\Lib\AuthStrategy\GoogleAuthStrategy;
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
    
    Session::start();
    $csrf = Str::random(40);
    $this->state = base64_encode(\json_encode([
        "organization_id" => $this->organization->id,
        "csrf" => $csrf
    ]));

    Session::put("csrf_state_token",$csrf);
});

it('registers a new user using Standard Auth Strategy', function() {
    $authService = new AuthService(app(\App\Services\User\UserService::class));

    $user = $authService->register([
        'first_name' => 'Teste',
        'email' => 'teste@gmail.com',
        'surname' => 'Usuário',
        'organization_id' => $this->organization->id,
    ], 'senhateste@123',null, $this->strategy);

    expect($user)->toBeInstanceOf(User::class)->and($user->email)->toBe("teste@gmail.com")
        ->and($user->first_name)->toBe("Teste")
        ->and($user->surname)->toBe("Usuário")
        ->and($user->organization_id)->toBe($this->organization->id)
        ->and($user->role)->toBe(\App\Enums\RolesEnum::ADMIN);
});

it('cannot register an existing user due to email already exists', function() {
    $authService = new AuthService(app(\App\Services\User\UserService::class));

    $user = User::factory()->create();

    $this->expectException(UserAlreadyRegisteredException::class);

    $authService->register([
        'first_name' => 'Teste',
        'email' => $user->email,
        'surname' => 'Usuário',
        'organization_id' => $this->organization->id,
    ], 'senhateste@123',null, $this->strategy);

});
