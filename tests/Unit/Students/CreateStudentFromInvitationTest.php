<?php

use App\Enums\GradeLevelEnum;
use App\Models\User;
use App\Enums\RolesEnum;
use App\Models\Organization;
use App\Models\Student;
use App\Repositories\StudentRepository;
use App\Repositories\UserRepository;
use App\Services\StudentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a student and its user from invitation data', function () {
    $service = new StudentService(new UserRepository,new StudentRepository);

    $data = [
        'email' => 'aluno@teste.com',
        'first_name' => 'Maria',
        'surname' => 'Souza',
        'organization_id' => Organization::factory()->create()->id,
        'registration_code' => '123456789',
        'grade_level' => GradeLevelEnum::EIGHTH_ELEMENTARY,
        'admission_date' => '2024-05-01',
    ];

    $student = $service->createFromInvitation($data);

    expect($student)->toBeInstanceOf(Student::class);
    expect($student->registration_code)->toBe($data['registration_code']);
    expect($student->grade_level)->toBe(GradeLevelEnum::EIGHTH_ELEMENTARY);
    expect($student->admission_date->format("Y-m-d"))->toBe($data['admission_date']);

    $user = $student->user;
    expect($user)->toBeInstanceOf(User::class);
    expect($user->email)->toBe($data['email']);
    expect($user->organization_id)->toBe($data['organization_id']);
    expect($user->role)->toBe(RolesEnum::STUDENT);
    expect($user->active)->toBeFalse();
});

