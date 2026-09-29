<?php

namespace Database\Factories;

use App\Models\Feedback;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Feedback>
 */
class FeedbackFactory extends Factory
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
            'menu_id' => \App\Models\Menu::factory(),
            'reserve_id' => \App\Models\Reserve::factory(),
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->sentence(10),
        ];
    }
}
