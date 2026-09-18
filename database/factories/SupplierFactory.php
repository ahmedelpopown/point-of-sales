<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Governorate;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    public function definition(): array
    {
        $governorate = Governorate::query()
            ->inRandomOrder()
            ->first();

        $city = $governorate
            ? City::query()
                ->where(
                    'governorate_id',
                    $governorate->id
                )
                ->inRandomOrder()
                ->first()
            : null;

        return [
            'name' => fake()->company(),

            'email' => fake()->unique()->safeEmail(),

            'address' => fake()->address(),

            'phone' => fake()->phoneNumber(),

            'governorate_id' => $governorate?->id,

            'city_id' => $city?->id,
        ];
    }
}