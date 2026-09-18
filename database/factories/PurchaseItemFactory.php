<?php

namespace Database\Factories;

use App\Models\PurchaseItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseItem>
 */
class PurchaseItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quantity' => fake()->numberBetween(1, 10),
            'unit_price' => fake()->randomFloat(2, 1, 50),
            'total_price' => fake()->randomFloat(2, 10, 500),
            'product_id' => fake()->numberBetween(1, 10),
            'purchase_id' => fake()->numberBetween(1,10),
        ];
    }
}
