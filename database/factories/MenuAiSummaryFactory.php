<?php

namespace Database\Factories;

use App\Models\MenuAiSummary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuAiSummary>
 */
class MenuAiSummaryFactory extends Factory
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
            'summary' => fake()->paragraph(),
        ];
    }
}
