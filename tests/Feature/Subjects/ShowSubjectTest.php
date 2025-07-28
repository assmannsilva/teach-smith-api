<?php

use App\Enums\RolesEnum;
use App\Models\Organization;
use App\Models\User;
use App\Models\Subject;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function() {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create([
        "role" => RolesEnum::ADMIN, 
        "organization_id" => $this->organization->id
    ]);
    actingAs($this->user);
    $this->subject = Subject::factory()->create(["organization_id" => $this->organization->id]);
});

test('show subject', function () {
    $response = getJson(route('subjects.show', $this->subject->id));
    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $this->subject->id,
        'name' => $this->subject->name,
        'classroom_id' => $this->subject->classroom_id,
        'teacher_id' => $this->subject->teacher_id,
    ]);
});

test('fail to show non-existent subject', function () {
    $response = getJson(route('subjects.show', 'non-existent-id'));
    $response->assertNotFound();
});
