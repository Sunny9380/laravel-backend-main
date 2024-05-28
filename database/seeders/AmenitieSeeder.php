<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmenitieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            [
                'name' => 'First aid and kit',
                'slug' => 'first-aid-and-kit',
                'icon' => 'Cross',
                'is_special' => 0,
            ],

            [
                'name' => 'Breakfast coffee Tea',
                'slug' => 'breakfast-coffee-tea',
                'icon' => 'UtensilsCrossed',
                'is_special' => 1,
            ],

            [
                'name' => 'Heater',
                'slug' => 'heater',
                'icon' => 'Flame',
                'is_special' => 0,
            ],

            [
                'name' => 'Shampoo',
                'slug' => 'shampoo',
                'icon' => 'ShowerHead',
                'is_special' => 0,
            ],

            [
                'name' => 'Laptop Friendly Workspace',
                'slug' => 'laptop-friendly-workspace',
                'icon' => 'LampDesk',
                'is_special' => 1,
            ],

            [
                'name' => 'Air Conditioning',
                'slug' => 'air-conditioning',
                'icon' => 'AirVent',
                'is_special' => 1,
            ],

            [
                'name' => 'Coffee',
                'slug' => 'coffee',
                'icon' => 'Coffee',
                'is_special' => 0,
            ],

            [
                'name' => 'Kitchen',
                'slug' => 'kitchen',
                'icon' => 'ChefHat',
                'is_special' => 0,
            ],

            [
                'name' => 'Carbon monoxide alarm',
                'slug' => 'carbon-monoxide-alarm',
                'icon' => 'Bell',
                'is_special' => 1,
            ],

            [
                'name' => 'Wifi',
                'slug' => 'wifi',
                'icon' => 'Wifi',
                'is_special' => 0,
            ],

            [
                'name' => 'Free street parking',
                'slug' => 'free-street-parking',
                'icon' => 'ParkingCircle',
                'is_special' => 1,
            ],
        ];

        foreach ($amenities as $amenitie) {
            DB::table('amenities')->insert($amenitie);
        }
    }
}
