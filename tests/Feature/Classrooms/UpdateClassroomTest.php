<?php

use App\Enums\RolesEnum;
use App\Models\User;
use App\Models\Classroom;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\putJson;
use function Pest\Laravel\postJson;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function() {
    $this->user = User::factory()->create(["role" => RolesEnum::ADMIN]);
    actingAs($this->user);
    $this->classroom = Classroom::factory()->create();
});

test('update classroom', function () {
    $payload = [
        'grade' => '6º Ano',
        'education_stage' => 'Fundamental',
        'section' => 'B',
        'year' => now()->year,
    ];

    $response = putJson(route('classrooms.update', $this->classroom->id), $payload);
    $response->assertOk();
    $response->assertJsonFragment($payload);
    $this->assertDatabaseHas('classrooms', $payload);
});

test('fail to update classroom with invalid data', function () {
    $payload = [
        'grade' => '',
        'education_stage' => '',
        'section' => '',
        'year' => 1900,
    ];
    $response = putJson(route('classrooms.update', $this->classroom->id), $payload);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['grade', 'education_stage', 'section', 'year']);
});

test('fail to update non-existent classroom', function () {
    $payload = [
        'grade' => '6º Ano',
        'education_stage' => 'Fundamental',
        'section' => 'B',
        'year' => now()->year,
    ];
    $response = putJson(route('classrooms.update', 'non-existent-id'), $payload);
    $response->assertNotFound();
});
