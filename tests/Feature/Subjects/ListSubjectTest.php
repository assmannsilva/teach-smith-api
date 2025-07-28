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
    $this->subjects = Subject::factory()->count(2)->create(["organization_id" => $this->organization->id]);
});

test('list subjects', function () {
    $response = getJson(route('subjects.index'));
    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});
