<?php

namespace Database\Factories;

use App\Models\ScholarshipApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScholarshipApplication>
 */
class ScholarshipApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => \App\Models\Student::factory(),
            'admin_note' => fake()->optional()->sentence(),
            'student_note' => fake()->optional()->sentence(),
        ];
    }
}
