<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(
                fake()->numberBetween(1, 3),
                true
            ),

            'description' => fake()->sentence(),

            'barcode' => fake()->unique()->ean13(),

            'status' => 'active',

            'image' => null,

            'current_quantity' => fake()->numberBetween(
                300,
                1000
            ),

            'price' => fake()->randomFloat(
                2,
                100,
                1500
            ),
        ];
    }
}