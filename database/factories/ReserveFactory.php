<?php

namespace Database\Factories;

use App\Enums\ReserveStatus;
use App\Models\Reserve;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reserve>
 */
class ReserveFactory extends Factory
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
            'student_id' => \App\Models\Student::factory(),
            'price' => fake()->randomFloat(2, 0, 15),
            'secret_barcode' => \Illuminate\Support\Str::uuid()->toString(),
            'status' => ReserveStatus::ACTIVE ,
        ];
    }

    public function delivered(){ 
        return $this->state(fn (array $attributes) => [
            'status' => ReserveStatus::DELIVERED,
        ]);
    }


}
