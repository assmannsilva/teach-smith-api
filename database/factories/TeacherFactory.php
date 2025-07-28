<?php

namespace Database\Factories;

use App\Enums\RolesEnum;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class TeacherFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
           'user_id' => User::factory(["role" => RolesEnum::TEACHER]),
            'cpf' => fake()->unique()->numerify('###########'),
            'bio' => fake()->text(200),
            'degree' => fake()->randomElement(['Bachelor', 'Master', 'PhD']),
            'hire_date' => fake()->date(),
        ];
    }
}
