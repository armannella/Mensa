<?php

namespace Database\Factories;

use App\Enums\MealEnum;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'canteen_id' => \App\Models\Canteen::factory(),
            'meal' => fake()->randomElement(MealEnum::cases()),
        ];
    }
}
