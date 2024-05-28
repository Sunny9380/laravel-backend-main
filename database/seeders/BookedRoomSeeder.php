<?php

namespace Database\Seeders;

use App\Models\BookedRoom;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookedRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookedRooms = [
            [
                'booking_id' => 1,
                'room_id' => 1,
                'room_count' => 2,
                'guest_count' => 3,
            ],
            [
                'booking_id' => 2,
                'room_id' => 2,
                'room_count' => 1,
                'guest_count' => 1,
            ],
            [
                'booking_id' => 3,
                'room_id' => 3,
                'room_count' => 2,
                'guest_count' => 4,
            ],
            [
                'booking_id' => 4,
                'room_id' => 4,
                'room_count' => 1,
                'guest_count' => 2,
            ],
            [
                'booking_id' => 5,
                'room_id' => 5,
                'room_count' => 3,
                'guest_count' => 6,
            ],
            [
                'booking_id' => 6,
                'room_id' => 1,
                'room_count' => 1,
                'guest_count' => 2,
            ],
            [
                'booking_id' => 7,
                'room_id' => 2,
                'room_count' => 2,
                'guest_count' => 3,
            ],
            [
                'booking_id' => 8,
                'room_id' => 3,
                'room_count' => 1,
                'guest_count' => 1,
            ],
            [
                'booking_id' => 9,
                'room_id' => 4,
                'room_count' => 3,
                'guest_count' => 4,
            ],
        ];

        foreach ($bookedRooms as $booking) {
            BookedRoom::create($booking);
        }
    }
}
