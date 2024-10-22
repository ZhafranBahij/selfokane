<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
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
            'budget_source_id' => rand(1, 6),
            'transaction_type_id' => rand(1, 2),
            'category_id' => rand(1, 2),
            'nominal' => rand(-1_000_000, 1_000_000),
            'description' => fake()->words(5, true),
            'date' => now(),
        ];
    }
}
