<?php

namespace Database\Factories;

use App\Models\Debt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Debt>
 */
class DebtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'price' => fake()->randomFloat(2, 10, 500),
            'quantity' => fake()->numberBetween(1, 10),
            'date' => fake()->date(),
            'payment' => fake()->randomFloat(2, 0, 500),
            'employee_id' => fake()->numberBetween(1, 10),
        ];
    }
}
