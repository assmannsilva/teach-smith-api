<?php

use App\Enums\RolesEnum;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

beforeEach(function() {
    actingAs(User::factory()->create(["role" => RolesEnum::ADMIN]));
    $this->teacher1 = Teacher::factory()->create();
    $this->teacher2 = Teacher::factory()->create();
});

test('search teachers by first name', function () {
    $response = getJson(route('teachers.search', ['name' => $this->teacher1->user->first_name]));
    $response->assertOk();
    $response->assertJsonFragment([
        'first_name' => $this->teacher1->user->first_name,
        'surname' => $this->teacher1->user->surname,
    ]);
});

test('search teachers by surname', function () {
    $response = getJson(route('teachers.search', ['name' => $this->teacher2->user->surname]));
    $response->assertOk();
    $response->assertJsonFragment([
        'first_name' => $this->teacher2->user->first_name,
        'surname' => $this->teacher2->user->surname,
    ]);
});

test('search teachers with no match', function () {
    $response = getJson(route('teachers.search', ['name' => 'Inexistente']));
    $response->assertOk();
    $response->assertJsonCount(0);
});

test('search teachers returns at most 6 results', function () {
    // Create 10 teachers
    foreach (range(1, 10) as $i) {
        $user = User::factory()->create([
            'first_name' => 'Ana',
            'surname' => 'Teste' . $i,
            'role' => RolesEnum::TEACHER,
        ]);
        Teacher::factory()->create([
            'user_id' => $user->id,
        ]);
    }
    $response = getJson(route('teachers.search', ['name' => 'Ana']));
    $response->assertOk();
    $response->assertJsonCount(6); // Limit defined in the service
});
