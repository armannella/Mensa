<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\Menu;
use App\Models\Student;
use App\Models\WaitList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WaitList>
 */
class WaitListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'menu_id' => Menu::factory() ,
            'food_id' => Food::factory(),
            'price' => fake()->randomFloat(2, 0, 20),
        ];
    }
}
