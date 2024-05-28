<?php

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'state_id' => State::query()->inRandomOrder()->first()->id,
            'name' => fake()->name,
            'image' => fake()->image,
            'number_of_booking' => fake()->numberBetween(0, 100),
            'number_of_hotels' => fake()->numberBetween(0, 100),
            'is_stopped' => false,
        ];
    }
}
