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

it("sucessfully bulk all students invites", function () {
    Queue::fake();
    $csvContent = <<<CSV
email,registration_code,first_name,surname,grade_level,admission_date
joao.silva@example.com,"39053344705",João,Silva,9º Ano,2023-02-15
maria.oliveira@example.com,"62570585676",Maria,Oliveira,8º Ano,2022-11-10
lucas.pereira@example.com,"80110449080",Lucas,Pereira,1º Ano,2024-01-20
ana.souza@example.com,"55634583006",Ana,Souza,2º Ano,2023-03-05
pedro.lima@example.com,"84364634066",Pedro,Lima,3º Ano,2023-07-01
CSV; //heredoc cannot be idented

    $file = UploadedFile::fake()->createWithContent('students.csv', $csvContent);

    $response = $this->post(route('students.bulk-invite'), [
        'import_file' => $file,
    ]);

      $response->assertStatus(200)
             ->assertJson([
                 'dispatched_count' => 5,
                 'duplicated_emails' => [],
                 'errors' => [],
                 'messsage' => 'Invites dispatched',
             ]);

    Queue::assertPushed(CreateUserRegistration::class, 5);
});

it("partial success on bulking students invites", function () {
    Queue::fake();
    User::factory()->create(['email' => 'caueteste@gmail.com']);

    $csvContent = <<<CSV
email,registration_code,first_name,surname,grade_level,admission_date
caueteste@gmail.com,"39053344705",João,Silva,9º Ano,2023-02-15
maria.oliveira@example.com,"62570585676",Maria,Oliveira,8º Ano,2022-11-10
lucas.pereira@example.com,"80110449080",Lucas,Pereira,1º Ano,2024-01-20
ana.souza@example.com,"55634583006",Ana,Souza,2º Ano,01/01/2024
pedro.lima@example.com,"84364634066",Pedro,Lima,3º Ano,2023-07-01
CSV; //heredoc cannot be idented

    $file = UploadedFile::fake()->createWithContent('students.csv', $csvContent);

    $response = $this->post(route('students.bulk-invite'), [
        'import_file' => $file,
    ]);

    $response->assertStatus(207)
    ->assertJson([
        'dispatched_count' => 3,
        'duplicated_emails' => ["caueteste@gmail.com"],
        'errors' => [
            [
                'errors' => [
                    'The admission date field must match the format Y-m-d.'
                ],
                "row" => 5
            ]
        ],
        'messsage' => 'Invites dispatched',
    ]);

    Queue::assertPushed(CreateUserRegistration::class, 3);
});


it("non success on bulking students invites", function () {
    Queue::fake();
    User::factory()->create(['email' => 'caueteste@gmail.com']);

    $csvContent = <<<CSV
email,registration_code,first_name,surname,grade_level,admission_date
caueteste@gmail.com,"39053344705",João,Silva,9º Ano,2023-02-15
,,,,,
pedro.lima,"84364634066",Pedro,Lima,3º Ano,01/01/2025
CSV; //heredoc cannot be idented

    $file = UploadedFile::fake()->createWithContent('students.csv', $csvContent);

    $response = $this->post(route('students.bulk-invite'), [
        'import_file' => $file,
    ]);

    $response->assertStatus(422)
    ->assertJson([
        'dispatched_count' => 0,
        'duplicated_emails' => ["caueteste@gmail.com"],
        'errors' => [
            [
                'errors' => [
                    'The email field is required.',
                    'The registration code field is required.',
                    'The first name field is required.',
                    'The surname field is required.',
                    'The grade level field is required.',
                    'The admission date field is required.',
                ],
                "row" => 3
            ],
            [
                'errors' => [
                    'The email field must be a valid email address.',
                ],
                "row" => 4
            ]
        ],
        'messsage' => 'Could not dispatch invites',
    ]);

    Queue::assertPushed(CreateUserRegistration::class, 0);
});

