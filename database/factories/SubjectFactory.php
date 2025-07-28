<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\Organization;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class SubjectFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->text(100),
            'classroom_id' => Classroom::factory(),
            'teacher_id' => Teacher::factory(),
            'organization_id' => Organization::factory(),
        ];
    }
}
