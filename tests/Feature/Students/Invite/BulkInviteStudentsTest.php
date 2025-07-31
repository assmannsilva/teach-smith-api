<?php

use App\Enums\RolesEnum;
use App\Jobs\CreateUserRegistration;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\UploadedFile;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function() {
    $this->user = User::factory()->create(["role" => RolesEnum::ADMIN]);
    actingAs($this->user);
});

it("sucessfully bulk all students invites", function () {
    Classroom::factory()->create([
        'grade' => '9º Ano', 
        'section' => 'A',
        "organization_id" => $this->user->organization_id
    ]);
    Queue::fake();
    $csvContent = <<<CSV
email,registration_code,first_name,surname,grade,section,admission_date
joao.silva@example.com,"39053344705",João,Silva,9º Ano,A,2023-02-15
maria.oliveira@example.com,"62570585676",Maria,Oliveira,9º Ano,A,2022-11-10
lucas.pereira@example.com,"80110449080",Lucas,Pereira,9º Ano,A,2024-01-20
ana.souza@example.com,"55634583006",Ana,Souza,9º Ano,A,2023-03-05
pedro.lima@example.com,"84364634066",Pedro,Lima,9º Ano,A,2023-07-01
CSV; //heredoc cannot be idented

    $file = UploadedFile::fake()->createWithContent('students.csv', $csvContent);

    $response = $this->post(route('students.bulk-invite'), [
        'import_file' => $file,
    ]);

      $response->assertStatus(200)
             ->assertJson([
                 'dispatched' => 5,
                 'errors' => [],
             ]);

    Queue::assertPushed(CreateUserRegistration::class, 5);
});

it("partial success on bulking students invites", function () {
    Queue::fake();
    
    User::factory()->create(['email' => 'caueteste@gmail.com']);
    Classroom::factory()->create(['grade' => '9º Ano', 'section' => 'A', "organization_id" => $this->user->organization_id]);
    Classroom::factory()->create(['grade' => '1º Ano', 'section' => 'C', "organization_id" => $this->user->organization_id]);

    $csvContent = <<<CSV
email,registration_code,first_name,surname,grade,section,admission_date
caueteste@gmail.com,"39053344705",João,Silva,9º Ano,A,2023-02-15
maria.oliveira@example.com,"62570585676",Maria,Oliveira,9º Ano,A,2022-11-10
lucas.pereira@example.com,"80110449080",Lucas,Pereira,1º Ano,C,2024-01-20
ana.souza@example.com,"55634583006",Ana,Souza,9º Ano,A,01/01/2024
pedro.lima@example.com,"84364634066",Pedro,Lima,1º Ano,C,2023-07-01
alvario.lima@example.com,"22244902080",Alvario,Lima,1º Ano,A,2023-07-01
leonardo.lima@example.com,"14509507011",Leonardo,Lima,2º Ano,A,2023-07-01
CSV; //heredoc cannot be idented

    $file = UploadedFile::fake()->createWithContent('students.csv', $csvContent);

    $response = $this->post(route('students.bulk-invite'), [
        'import_file' => $file,
    ]);

    $response->assertStatus(207)
    ->assertJson([
        'dispatched' => 3,
        'errors' => [
            "1 rows contain incorrect or missing data out",
            "1 email is already registered",
            "2 classrooms do not exist among eligible users.",
        ]
    ]);

    Queue::assertPushed(CreateUserRegistration::class, 3);
});


it("non success on bulking students invites", function () {
    Queue::fake();
    User::factory()->create(['email' => 'caueteste@gmail.com']);

    $csvContent = <<<CSV
email,registration_code,first_name,surname,grade,section,admission_date
caueteste@gmail.com,"39053344705",João,Silva,9º Ano,A,2023-02-15
,,,,,,,
pedro.lima,"84364634066",Pedro,Lima,3º Ano,9º Ano,A,01/01/2025
CSV; //heredoc cannot be idented

    $file = UploadedFile::fake()->createWithContent('students.csv', $csvContent);

    $response = $this->post(route('students.bulk-invite'), [
        'import_file' => $file,
    ]);

    $response->assertStatus(422)
    ->assertJson([
        'dispatched' => 0,
        'errors' => [
            "2 rows contain incorrect or missing data out",
            "1 email is already registered",
        ]
    ]);

    Queue::assertPushed(CreateUserRegistration::class, 0);
});

