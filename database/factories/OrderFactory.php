<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'total' => 0,

            'user_id' => User::query()
                ->inRandomOrder()
                ->value('id'),

            'employee_id' => Employee::query()
                ->where('status', 'active')
                ->inRandomOrder()
                ->value('id'),
        ];
    }
}