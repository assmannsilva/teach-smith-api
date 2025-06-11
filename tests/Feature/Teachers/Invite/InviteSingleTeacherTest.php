<?php

use App\Enums\RolesEnum;
use App\Jobs\CreateUserRegistration;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function() {
    actingAs(User::factory()->create(["role" => RolesEnum::ADMIN]));
});

it('invites a teacher when email is not registered', function () {
    Queue::fake();

    $payload = [
        'first_name' => 'Ana',
        'email' => 'ana.nova@escola.com',
        'cpf' => '829.994.760-00',
        'surname' => 'Silva',
        'degree' => 'Mestrado',
        'hire_date' => '2024-06-01',
    ];

    $response = $this->postJson(route('teachers.invite'), $payload);

    $response->assertStatus(200)
             ->assertJson(['message' => 'Invite dispatched']);

    Queue::assertPushed(CreateUserRegistration::class);
});

it('does not invite a teacher if email is already registered', function () {
    Queue::fake();
    User::factory()->create(['email' => 'ana.jaexiste@escola.com']);

    $payload = [
        'first_name' => 'Ana',
        'email' => "ana.jaexiste@escola.com",
        'cpf' => '829.994.760-00',
        'surname' => 'Silva',
        'degree' => 'Mestrado',
        'hire_date' => '2024-06-01',
    ];

    $response = $this->postJson(route('teachers.invite'), $payload);

    $response->assertStatus(400)
             ->assertJson(['message' => 'Already registered email']);

    Queue::assertNothingPushed();
});

it('fails validation when required fields are missing or invalid', function () {

    $invalidPayload = [
        'email' => 'invalid-email',
        'cpf' => 'invalid-cpf',
        'first_name' => '', 
        'surname' => null, 
        'degree' => '',
        'hire_date' => '31/12/2025',
    ];

    $response = $this->postJson(route('teachers.invite'), $invalidPayload);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'email',
        'cpf',
        'first_name',
        'surname',
        'degree',
        'hire_date',
    ]);
});



