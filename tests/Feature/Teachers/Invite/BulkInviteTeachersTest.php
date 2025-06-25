<?php

use App\Enums\RolesEnum;
use App\Jobs\CreateUserRegistration;
use App\Models\User;
use Illuminate\Http\UploadedFile;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function() {
    actingAs(User::factory()->create(["role" => RolesEnum::ADMIN]));
});

it("sucessfully bulk all teachers invites", function () {
    Queue::fake();
    $csvContent = <<<CSV
first_name,email,cpf,surname,degree,hire_date
Caue,caueteste@gmail.com,466.462.660-69,Assmann Silva,Doutorado,2025-06-06
Ana,ana.silva@gmail.com,829.994.760-00,Silva Souza,Mestrado,2023-08-12
Bruno,bruno.costa@gmail.com,992.478.200-30,Costa Lima,Especialização,2024-01-15
Carla,carla.rodrigues@gmail.com,719.079.740-83,Rodrigues Melo,Doutorado,2025-03-30
CSV; //heredoc cannot be idented

    $file = UploadedFile::fake()->createWithContent('teachers.csv', $csvContent);

    $response = $this->post(route('teachers.bulk-invite'), [
        'import_file' => $file,
    ]);

      $response->assertStatus(200)
             ->assertJson([
                 'dispatched' => 4,
                 'errors' => [],
             ]);

    Queue::assertPushed(CreateUserRegistration::class, 4);
});

it("partial success on bulking teachers invites", function () {
    Queue::fake();
    User::factory()->create(['email' => 'caueteste@gmail.com']);

    $csvContent = <<<CSV
first_name,email,cpf,surname,degree,hire_date
Caue,caueteste@gmail.com,466.462.660-69,Assmann Silva,Doutorado,2025-06-06
Ana,ana.silva@gmail.com,829.994.760-00,Silva Souza,Mestrado,2023-08-12
Bruno,bruno.costa@gmail.com,992.478.200-30,Costa Lima,Especialização,2024-01-15
Carla,carla.rodrigues@gmail.com,719.079.740-83,Rodrigues Melo,Doutorado,2025-03-30
Dom,dom@gmail.com,123.456.769-50,Domingo Silva,Doutorado,06/06/2025
CSV; //heredoc cannot be idented

    $file = UploadedFile::fake()->createWithContent('teachers.csv', $csvContent);

    $response = $this->post(route('teachers.bulk-invite'), [
        'import_file' => $file,
    ]);

    $response->assertStatus(207)
    ->assertJson([
        'dispatched' => 3,
        'errors' => [
            "1 rows contain incorrect or missing data out",
            "1 emails are already registered between valid rows",
        ]
    ]);

    Queue::assertPushed(CreateUserRegistration::class, 3);
});


it("non success on bulking teachers invites", function () {
    Queue::fake();
    User::factory()->create(['email' => 'caueteste@gmail.com']);

    $csvContent = <<<CSV
first_name,email,cpf,surname,degree,hire_date
Caue,caueteste@gmail.com,466.462.660-69,Assmann Silva,Doutorado,2025-06-06
,,,,,,
Dom,donunes,829.994.760-00,Domingo Silva,,2025-08-12
CSV; //heredoc cannot be idented

    $file = UploadedFile::fake()->createWithContent('teachers.csv', $csvContent);

    $response = $this->post(route('teachers.bulk-invite'), [
        'import_file' => $file,
    ]);

    $response->assertStatus(422)
    ->assertJson([
        'dispatched' => 0,
        'errors' => [
            "2 rows contain incorrect or missing data out",
            "1 emails are already registered between valid rows",
        ]
    ]);

    Queue::assertPushed(CreateUserRegistration::class, 0);
});

