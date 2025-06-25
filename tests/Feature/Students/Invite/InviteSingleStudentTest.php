<?php

use App\Enums\RolesEnum;
use App\Jobs\CreateUserRegistration;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function() {
    $this->user = User::factory()->create(["role" => RolesEnum::ADMIN]);
    actingAs($this->user);
});

it('invites a student when email is not registered', function () {
    Queue::fake();
    Classroom::factory()->create([
        'grade' => '1° Ano', 
        'section' => 'A',
        "year" => Carbon::now()->year,
        "organization_id" => $this->user->organization_id
    ]);

    $payload = [
        'first_name' => 'Ana',
        'email' => 'ana.nova@escola.com',
        'registration_code' => '6543138',
        'surname' => 'Silva',
        'grade' => '1° Ano',
        'section' => 'A',
        'admission_date' => '2024-06-01',
    ];

    $response = $this->postJson(route('students.invite'), $payload);

    $response->assertStatus(200)
             ->assertJson(['message' => 'Invite dispatched']);

    Queue::assertPushed(CreateUserRegistration::class);
});

it('does not invite a student if email is already registered', function () {
    Queue::fake();
    User::factory()->create(['email' => 'ana.jaexiste@escola.com']);

    $payload = [
        'first_name' => 'Ana',
        'email' => "ana.jaexiste@escola.com",
        'registration_code' => '6543138',
        'surname' => 'Silva',
        'classroom' => '1° Ano',
        'admission_date' => '2024-06-01',
        'grade' => '1° Ano',
        'section' => 'A',
    ];

    $response = $this->postJson(route('students.invite'), $payload);

    $response->assertStatus(422)
             ->assertJson(['message' => 'email already registered']);

    Queue::assertNothingPushed();
});

it('does not invite a student if classrom does not exists', function () {
    Queue::fake();

    $payload = [
        'first_name' => 'Ana',
        'email' => "ana.jaexiste@escola.com",
        'registration_code' => '6543138',
        'surname' => 'Silva',
        'classroom' => '1° Ano',
        'admission_date' => '2024-06-01',
        'grade' => '1° Ano',
        'section' => 'A',
    ];

    $response = $this->postJson(route('students.invite'), $payload);

    $response->assertStatus(422)
             ->assertJson(['message' => 'classroom do not exist']);

    Queue::assertNothingPushed();
});

it('fails validation when required fields are missing or invalid', function () {

    $invalidPayload = [
        'email' => 'invalid-email',
        'registration_code' => '',
        'first_name' => '', 
        'surname' => null, 
        'classroom' => '',
        'admission_date' => '31/12/2025',
        'grade' => '',
        'section' => '',
    ];

    $response = $this->postJson(route('students.invite'), $invalidPayload);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'email',
        'registration_code',
        'first_name',
        'surname',
        'grade',
        "section",
        'admission_date',
    ]);
});



