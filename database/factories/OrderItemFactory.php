<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);

        $price = fake()->randomFloat(
            2,
            100,
            1500
        );

        return [
            'quantity' => $quantity,

            'price' => $price,

            'total' => $quantity * $price,

            'order_id' => Order::query()
                ->inRandomOrder()
                ->value('id'),

            'product_id' => Product::query()
                ->inRandomOrder()
                ->value('id'),
        ];
    }
}