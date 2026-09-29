<?php

namespace Database\Factories;

use App\Models\Canteen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Canteen>
 */
class CanteenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => fake()->randomElement(['Mensa Centrale', 'Mensa Papardo', 'Mensa Annunziata']),
            'address' => fake()->address(),
        ];
    }
}
