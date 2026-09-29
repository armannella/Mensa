<?php

namespace Database\Factories;

use App\Models\MenuDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuDetail>
 */
class MenuDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'menu_id' => \App\Models\Menu::factory(),
            'food_id' => \App\Models\Food::factory(),
            'capacity' => fake()->numberBetween(50, 200),
            'reserved' => 0,
            'daily_sale_capacity' => fake()->optional()->numberBetween(10, 30),
            'daily_sale_reserved' => 0,
        ];
    }
}
