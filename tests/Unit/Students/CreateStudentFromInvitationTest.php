<?php

use App\Models\User;
use App\Enums\RolesEnum;
use App\Models\Classroom;
use App\Models\Organization;
use App\Models\Student;
use App\Repositories\ClassroomRepository;
use App\Repositories\StudentRepository;
use App\Repositories\UserRepository;
use App\Services\StudentService;
use App\Services\User\InviteUserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('creates a student and its user from invitation data', function () {
    $service = new StudentService(
        new UserRepository,
        new StudentRepository,
        new ClassroomRepository, 
        new InviteUserService
    );
    $user = User::factory()->create(["role" => RolesEnum::ADMIN]);
    actingAs($user);

    Classroom::factory()->create([
        'grade' => '1° Ano', 
        'section' => 'B',
        "year" => Carbon::now()->year,
        "organization_id" => $user->organization->id
    ]);

    $data = [
        'email' => 'aluno@teste.com',
        'first_name' => 'Maria',
        'surname' => 'Souza',
        'organization_id' => $user->organization->id,
        'registration_code' => '123456789',
        'classroom' => null,
        'admission_date' => '2024-05-01',
        "grade" => "1° Ano",
        "section" => "B"
    ];

    $student = $service->createFromInvitation($data);

    expect($student)->toBeInstanceOf(Student::class);
    expect($student->registration_code)->toBe($data['registration_code']);
    //expect($student->classroom)->toBe(GradeLevelEnum::EIGHTH_ELEMENTARY);
    expect($student->admission_date->format("Y-m-d"))->toBe($data['admission_date']);

    $user = $student->user;
    expect($user)->toBeInstanceOf(User::class);
    expect($user->email)->toBe($data['email']);
    expect($user->organization_id)->toBe($data['organization_id']);
    expect($user->role)->toBe(RolesEnum::STUDENT);
    expect($user->active)->toBeFalse();
});

