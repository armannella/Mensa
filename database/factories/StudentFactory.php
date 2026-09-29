<?php

namespace Database\Factories;

use App\Models\DiscountPlan;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'user_id' => User::factory(),
            'codice_fiscale' => strtoupper(fake()->unique()->bothify('??????##?##?###?')),
            'matricola' => fake()->unique()->numberBetween(550000, 559999),
            'discount_plan_id' => DiscountPlan::factory(),
        ];
    }
}
