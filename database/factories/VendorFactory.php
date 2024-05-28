<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor>
 */
class VendorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::query()->inRandomOrder()->first()->id,
            'name' => fake()->name,
            'address' => fake()->address,
            'vendor_id' => fake()->uuid,
            'gst_number' => fake()->uuid,
            'phone_number' => fake()->phoneNumber,
            'email' => fake()->email,
            'commission' => fake()->randomFloat(2, 0, 100),
        ];
    }
}
