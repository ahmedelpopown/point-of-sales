<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),

            'last_name' => fake()->lastName(),

            'email' => fake()->unique()->safeEmail(),

            'password' => Hash::make('password'),

         

            'phone' => fake()->phoneNumber(),

            'address' => fake()->address(),

            'department' => fake()->randomElement([
                'sales',
                'inventory',
                'purchasing',
                'supplier',
            ]),

            'position' => fake()->randomElement([
                'sales associate',
                'inventory clerk',
                'purchasing coordinator',
                'supplier relationship manager',
            ]),

            'salary' => fake()->randomFloat(
                2,
                30000,
                80000
            ),

            'hire_date' => fake()->dateTimeBetween(
                '-5 years',
                'now'
            ),

            'status' => fake()->randomElement([
                'active',
                'active',
                'active',
                'inactive',
            ]),
        ];
    }
}