<?php

namespace Database\Factories;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wallet_id' => \App\Models\Wallet::factory(),
            'type' => fake()->randomElement(TransactionType::cases()),
            'amount' => fake()->randomFloat(2, 5, 100),
            'status' => TransactionStatus::SUCCESS,
            'note' => fake()->optional()->sentence(),
            'ref_id' => strtoupper(fake()->bothify('TRX-########')),
        ];
    }
}
