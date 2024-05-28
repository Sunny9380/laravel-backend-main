<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            [
                'hotel_id' => 1,
                'room_type_id' => 1,
                'image' => 'room-image-1.jpg',
                'num_of_rooms' => 5,
                'is_active' => 1,
                'meal_options' => 'Breakfast included',
                'room_size' => '30 sqm',
                'bed_type' => 'King size bed',
                'default_rate' => 150,
                'guest_charge' => 20,
                'num_guest' => 2,
            ],
            [
                'hotel_id' => 1,
                'room_type_id' => 2,
                'image' => 'room-image-2.jpg',
                'num_of_rooms' => 3,
                'is_active' => 1,
                'meal_options' => 'No meals included',
                'room_size' => '25 sqm',
                'bed_type' => 'Queen size bed',
                'default_rate' => 100,
                'guest_charge' => 15,
                'num_guest' => 2,
            ],
            [
                'hotel_id' => 2,
                'room_type_id' => 1,
                'image' => 'room-image-3.jpg',
                'num_of_rooms' => 7,
                'is_active' => 1,
                'meal_options' => 'Breakfast included',
                'room_size' => '35 sqm',
                'bed_type' => 'King size bed',
                'default_rate' => 180,
                'guest_charge' => 25,
                'num_guest' => 2,
            ],
            [
                'hotel_id' => 1,
                'room_type_id' => 1,
                'image' => 'room1.jpg',
                'num_of_rooms' => 5,
                'is_active' => 1,
                'meal_options' => 'Breakfast included',
                'room_size' => '40 sqm',
                'bed_type' => 'King size bed',
                'default_rate' => 200,
                'guest_charge' => 25,
                'num_guest' => 2,
            ],
            [
                'hotel_id' => 2,
                'room_type_id' => 2,
                'image' => 'room2.jpg',
                'num_of_rooms' => 3,
                'is_active' => 1,
                'meal_options' => 'No meals included',
                'room_size' => '30 sqm',
                'bed_type' => 'Queen size bed',
                'default_rate' => 150,
                'guest_charge' => 20,
                'num_guest' => 2,
            ],
            [
                'hotel_id' => 3,
                'room_type_id' => 1,
                'image' => 'room3.jpg',
                'num_of_rooms' => 6,
                'is_active' => 1,
                'meal_options' => 'Breakfast included',
                'room_size' => '45 sqm',
                'bed_type' => 'King size bed',
                'default_rate' => 220,
                'guest_charge' => 30,
                'num_guest' => 2,
            ],
            [
                'hotel_id' => 4,
                'room_type_id' => 2,
                'image' => 'room4.jpg',
                'num_of_rooms' => 4,
                'is_active' => 1,
                'meal_options' => 'No meals included',
                'room_size' => '35 sqm',
                'bed_type' => 'Queen size bed',
                'default_rate' => 170,
                'guest_charge' => 22,
                'num_guest' => 2,
            ],
            [
                'hotel_id' => 5,
                'room_type_id' => 1,
                'image' => 'room5.jpg',
                'num_of_rooms' => 8,
                'is_active' => 1,
                'meal_options' => 'Breakfast included',
                'room_size' => '50 sqm',
                'bed_type' => 'King size bed',
                'default_rate' => 250,
                'guest_charge' => 35,
                'num_guest' => 2,
            ],
            [
                'hotel_id' => 6,
                'room_type_id' => 2,
                'image' => 'room6.jpg',
                'num_of_rooms' => 2,
                'is_active' => 1,
                'meal_options' => 'No meals included',
                'room_size' => '40 sqm',
                'bed_type' => 'Queen size bed',
                'default_rate' => 180,
                'guest_charge' => 24,
                'num_guest' => 2,
            ],
            [
                'hotel_id' => 7,
                'room_type_id' => 1,
                'image' => 'room7.jpg',
                'num_of_rooms' => 5,
                'is_active' => 1,
                'meal_options' => 'Breakfast included',
                'room_size' => '35 sqm',
                'bed_type' => 'King size bed',
                'default_rate' => 210,
                'guest_charge' => 28,
                'num_guest' => 2,
            ],
            [
                'hotel_id' => 8,
                'room_type_id' => 2,
                'image' => 'room8.jpg',
                'num_of_rooms' => 3,
                'is_active' => 1,
                'meal_options' => 'No meals included',
                'room_size' => '30 sqm',
                'bed_type' => 'Queen size bed',
                'default_rate' => 160,
                'guest_charge' => 21,
                'num_guest' => 2,
            ],
            [
                'hotel_id' => 9,
                'room_type_id' => 1,
                'image' => 'room9.jpg',
                'num_of_rooms' => 7,
                'is_active' => 1,
                'meal_options' => 'Breakfast included',
                'room_size' => '40 sqm',
                'bed_type' => 'King size bed',
                'default_rate' => 190,
                'guest_charge' => 26,
                'num_guest' => 2,
            ],
        ];

        foreach ($rooms as $room) {
            DB::table('rooms')->insert($room);
        }
    }
}
