<?php

namespace Database\Factories;

use App\Models\DiscountPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DiscountPlan>
 */
class DiscountPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Tier 1', 'Tier 2', 'Tier 3']),
            'percentage' => fake()->randomElement([10, 50, 90]),
            'description' => fake()->sentence(),
        ];
    }
    public function fiftyPercent(){
        return $this->state(fn (array $attributes) => [
            'percentage' => 50,
        ]);
    }
}
