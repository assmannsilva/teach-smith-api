<?php

use App\Enums\RolesEnum;
use App\Models\User;
use App\Models\Classroom;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function() {
    $this->user = User::factory()->create(["role" => RolesEnum::ADMIN]);
    actingAs($this->user);
    $this->classroom = Classroom::factory()->create();
});

test('show classroom', function () {
    $response = getJson(route('classrooms.show', $this->classroom->id));
    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $this->classroom->id,
        'grade' => $this->classroom->grade,
        'education_stage' => $this->classroom->education_stage,
        'section' => $this->classroom->section,
        'year' => $this->classroom->year,
    ]);
});

test('fail to show non-existent classroom', function () {
    $response = getJson(route('classrooms.show', 'non-existent-id'));
    $response->assertNotFound();
});
