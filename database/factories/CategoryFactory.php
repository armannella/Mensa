<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Primo Piatto', 'Secondo Piatto', 'Contorno', 'Dessert']),
            'price' => fake()->randomFloat(2, 1, 8),
        ];
    }
}
