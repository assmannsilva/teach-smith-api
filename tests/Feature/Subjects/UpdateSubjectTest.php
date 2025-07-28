<?php

use App\Enums\RolesEnum;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Organization;
use App\Models\Teacher;
use App\Models\Subject;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\putJson;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function() {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create([
        "role" => RolesEnum::ADMIN, 
        "organization_id" => $this->organization->id
    ]);
    actingAs($this->user);
    $this->classroom = Classroom::factory(["organization_id" => $this->organization->id])->create();
    $this->teacher = Teacher::factory()->create();
    $this->subject = Subject::factory()->create(["organization_id" => $this->organization->id]);
});

test('update subject', function () {
    $payload = [
        'name' => 'Português',
        'classroom_id' => $this->classroom->id,
        'teacher_id' => $this->teacher->id,
    ];
    $response = putJson(route('subjects.update', $this->subject->id), $payload);
    $response->assertOk();
    $response->assertJsonFragment($payload);
    $this->assertDatabaseHas('subjects', $payload);
});

test('fail to update subject with invalid data', function () {
    $payload = [
        'name' => '',
        'classroom_id' => 'invalid',
        'teacher_id' => 'invalid',
    ];
    $response = putJson(route('subjects.update', $this->subject->id), $payload);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name', 'classroom_id', 'teacher_id']);
});

test('fail to update non-existent subject', function () {
    $payload = [
        'name' => 'Português',
        'classroom_id' => $this->classroom->id,
        'teacher_id' => $this->teacher->id,
    ];
    $response = putJson(route('subjects.update', 'non-existent-id'), $payload);
    $response->assertNotFound();
});
