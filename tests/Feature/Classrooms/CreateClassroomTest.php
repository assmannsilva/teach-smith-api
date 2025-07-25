<?php

use App\Enums\RolesEnum;
use App\Models\User;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function() {
    $organization = \App\Models\Organization::factory()->create();
    $this->user = User::factory()->create(["role" => RolesEnum::ADMIN, "organization_id" => $organization->id]);
    actingAs($this->user);
});

test('successful classroom creation', function () {
    $payload = [
        'grade' => '5º Ano',
        'education_stage' => 'Fundamental',
        'section' => 'A',
        'year' => now()->year,
    ];

    $response = $this->postJson(route('classrooms.store'), $payload);

    $response->assertCreated();
    $response->assertJsonFragment($payload);
    $this->assertDatabaseHas('classrooms', $payload);
});

test('fail to create classroom with invalid data', function () {
    $payload = [
        'grade' => '',
        'education_stage' => '',
        'section' => '',
        'year' => 1900,
    ];
    $response = $this->postJson(route('classrooms.store'), $payload);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['grade', 'education_stage', 'section', 'year']);
});
