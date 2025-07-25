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
});

test('list classrooms', function () {
    Classroom::factory()->count(2)->create();
    $response = getJson(route('classrooms.index'));
    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});
