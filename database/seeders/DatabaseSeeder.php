<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(
            [
                StateSeeder::class,
                CitySeeder::class,
                UserSeed::class,
                LocationSeeder::class,
                VendorSeeder::class,
                PropertyTypeSeeder::class,
                AmenitieSeeder::class,
                HotelSeeder::class,
                HotelGallerieSeeder::class,
                RoomSeeder::class,
                RoomTypeSeeder::class,
                BlogSeeder::class,
                BookingSeeder::class,
                BookingTypeSeeder::class,
                BookedRoomSeeder::class,
                RatingSeeder::class,
            ]
        );
    }
}
