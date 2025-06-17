<?php

use App\Models\Teacher;
use App\Models\User;
use App\Enums\RolesEnum;
use App\Models\Organization;
use App\Repositories\TeacherRepository;
use App\Repositories\UserRepository;
use App\Services\TeacherService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a teacher and its user from invitation data', function () {
    $service = new TeacherService(new UserRepository,new TeacherRepository);

    $data = [
        'email' => 'professor@teste.com',
        'first_name' => 'Maria',
        'surname' => 'Souza',
        'organization_id' => Organization::factory()->create()->id,
        'cpf' => '295.379.470-87',
        'degree' => 'Mestrado',
        'hire_date' => '2024-05-01',
    ];

    $teacher = $service->createFromInvitation($data);

    expect($teacher)->toBeInstanceOf(Teacher::class);
    expect($teacher->cpf)->toBe($data['cpf']);
    expect($teacher->degree)->toBe($data['degree']);
    expect($teacher->hire_date->format("Y-m-d"))->toBe($data['hire_date']);

    $user = $teacher->user;
    expect($user)->toBeInstanceOf(User::class);
    expect($user->email)->toBe($data['email']);
    expect($user->organization_id)->toBe($data['organization_id']);
    expect($user->role)->toBe(RolesEnum::TEACHER);
    expect($user->active)->toBeFalse();
});

