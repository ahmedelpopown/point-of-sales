<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'total_price' => 0,

            'supplier_id' => Supplier::query()
                ->inRandomOrder()
                ->value('id'),

            'employee_id' => Employee::query()
                ->inRandomOrder()
                ->value('id'),
        ];
    }
}