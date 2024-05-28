<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HotelGallerieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotelGallerie = [
            [
                'hotel_id' => 1,
                'image' => 'hotel-image-8.jpg',
            ],
            [
                'hotel_id' => 2,
                'image' => 'hotel-image-9.jpg',
            ],
            [
                'hotel_id' => 3,
                'image' => 'hotel-image-10.jpg',
            ],
            [
                'hotel_id' => 4,
                'image' => 'hotel-image-11.jpg',
            ],
            [
                'hotel_id' => 5,
                'image' => 'hotel-image-12.jpg',
            ],
            [
                'hotel_id' => 6,
                'image' => 'hotel-image-13.jpg',
            ],
        ];

        foreach ($hotelGallerie as $hotel) {
            DB::table('hotel_galleries')->insert($hotel);
        }
    }
}
