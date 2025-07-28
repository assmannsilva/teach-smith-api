<?php

use App\Enums\RolesEnum;
use App\Models\Organization;
use App\Models\User;
use App\Models\Subject;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;

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

test('delete subject', function () {
    $response = deleteJson(route('subjects.destroy', $this->subject->id));
    $response->assertNoContent();
    $this->assertDatabaseMissing('subjects', [
        'id' => $this->subject->id,
    ]);
});

test('fail to delete non-existent subject', function () {
    $response = deleteJson(route('subjects.destroy', 'non-existent-id'));
    $response->assertNotFound();
});
