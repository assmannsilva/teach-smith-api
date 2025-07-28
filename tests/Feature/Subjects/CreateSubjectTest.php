<?php

use App\Enums\RolesEnum;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Organization;
use App\Models\Teacher;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function() {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create([
        "role" => RolesEnum::ADMIN, 
        "organization_id" => $this->organization->id
    ]);
    actingAs($this->user);
    $this->classroom = Classroom::factory()->create(["organization_id" => $this->organization->id]);
    $this->teacher = Teacher::factory()->create();
});

test('successful subject creation', function () {
    $payload = [
        'name' => 'Matemática',
        'classroom_id' => $this->classroom->id,
        'teacher_id' => $this->teacher->id,
    ];
    $response = postJson(route('subjects.store'), $payload);
    $response->assertCreated();
    $response->assertJsonFragment($payload);
    $this->assertDatabaseHas('subjects', $payload);
});

test('fail to create subject with invalid data', function () {
    $payload = [
        'name' => '',
        'classroom_id' => 'invalid',
        'teacher_id' => 'invalid',
    ];
    $response = postJson(route('subjects.store'), $payload);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name', 'classroom_id', 'teacher_id']);
});
