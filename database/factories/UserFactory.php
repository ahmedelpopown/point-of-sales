<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Governorate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
$governorate = \App\Models\Governorate::inRandomOrder()->first();

    // 2. هات مدينة واحدة عشوائية تكون تابعة للمحافظة دي
    $city = \App\Models\City::where('governorate_id', $governorate->id)
                ->inRandomOrder()
                ->first();
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'age' => fake()->numberBetween(20,60),
            'gender' => fake()->randomElement(['male','female']),
        
            'governorate_id' => $governorate->id,
            'city_id'=>City::where('governorate_id',$governorate->id)->first()
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
