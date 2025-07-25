<?php

use App\Enums\RolesEnum;
use App\Models\User;
use App\Models\Classroom;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function() {
    $this->user = User::factory()->create(["role" => RolesEnum::ADMIN]);
    actingAs($this->user);
    $this->classroom = Classroom::factory()->create();
});

test('delete classroom', function () {
    $response = deleteJson(route('classrooms.destroy', $this->classroom->id));
    $response->assertNoContent();
    $this->assertDatabaseMissing('classrooms', [
        'id' => $this->classroom->id,
    ]);
});

test('fail to delete non-existent classroom', function () {
    $response = deleteJson(route('classrooms.destroy', 'non-existent-id'));
    $response->assertNotFound();
});
