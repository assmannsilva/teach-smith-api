<?php

use App\Enums\RolesEnum;
use App\Models\Organization;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\User\UserService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can register an admin user', function () {
    $userData = [
        'first_name' => 'Jane',
        'surname' => 'Smith',
        'email' => 'jane@example.com',
        'password' => 'supersecret123@12',
        'organization_id' => Organization::factory()->create()->id,
    ];

    $userService = new UserService(new UserRepository());

    $user = $userService->registerAdminUser($userData);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->email)->toBe('jane@example.com');
    expect($user->role)->toBe(RolesEnum::ADMIN);
    expect($user->active)->toBeTrue();
});
