<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ClassroomFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'grade' => fake()->randomElement(['1° Ano','2° Ano','3° Ano']),
            'section' => \fake()->randomElement(['A', 'B', 'C']),
            'education_stage' => \fake()->randomElement(['Fundamental', 'Médio']),
            'year' => \date('Y'),
            'organization_id' => Organization::factory(),
        ];
    }
}
