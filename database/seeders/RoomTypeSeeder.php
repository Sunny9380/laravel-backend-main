<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = [
            [
                'name' => 'Luxury Suite',
                'slug' => 'luxury-suite',
                'image' => 'luxury-suite.jpg',
                'description' => 'Indulge in luxury with our spacious and elegant suites. Each suite features modern amenities and stunning views, providing you with a comfortable and memorable stay.',
                'status' => 1,
            ],
            [
                'name' => 'Executive Room',
                'slug' => 'executive-room',
                'image' => 'executive-room.jpg',
                'description' => 'Experience comfort and convenience in our executive rooms. Perfect for business travelers or those seeking a comfortable retreat, these rooms offer all the amenities you need for a pleasant stay.',
                'status' => 1,
            ],
            [
                'name' => 'Deluxe Double',
                'slug' => 'deluxe-double',
                'image' => 'deluxe-double.jpg',
                'description' => 'Our deluxe double rooms are designed for comfort and relaxation. With stylish furnishings and modern conveniences, these rooms provide the perfect retreat after a day of exploring.',
                'status' => 1,
            ],
            [
                'name' => 'Standard Room',
                'slug' => 'standard-room',
                'image' => 'standard-room.jpg',
                'description' => 'Enjoy a comfortable stay in our standard rooms. These cozy and inviting rooms are ideal for solo travelers or couples looking for a budget-friendly option without sacrificing comfort.',
                'status' => 1,
            ],
            [
                'name' => 'Family Suite',
                'slug' => 'family-suite',
                'image' => 'family-suite.jpg',
                'description' => 'Treat your family to a memorable stay in our spacious family suites. With separate sleeping and living areas, these suites offer plenty of space for everyone to relax and unwind.',
                'status' => 1,
            ],
            [
                'name' => 'Honeymoon Suite',
                'slug' => 'honeymoon-suite',
                'image' => 'honeymoon-suite.jpg',
                'description' => 'Celebrate your love in our romantic honeymoon suites. Featuring luxurious amenities and breathtaking views, these suites provide the perfect setting for your special occasion.',
                'status' => 1,
            ],
            [
                'name' => 'Penthouse Suite',
                'slug' => 'penthouse-suite',
                'image' => 'penthouse-suite.jpg',
                'description' => 'Experience the height of luxury in our exclusive penthouse suites. With spacious living areas, private balconies, and panoramic views, these suites offer an unforgettable stay.',
                'status' => 1,
            ],
            [
                'name' => 'Beachfront Villa',
                'slug' => 'beachfront-villa',
                'image' => 'beachfront-villa.jpg',
                'description' => 'Escape to paradise in our luxurious beachfront villas. With direct access to the beach and private swimming pools, these villas provide the ultimate retreat for relaxation and rejuvenation.',
                'status' => 1,
            ],
            [
                'name' => 'Mountain View Chalet',
                'slug' => 'mountain-view-chalet',
                'image' => 'mountain-view-chalet.jpg',
                'description' => 'Experience nature at its best in our charming mountain view chalets. Nestled amidst scenic landscapes, these chalets offer a cozy and tranquil escape from the hustle and bustle of city life.',
                'status' => 1,
            ],
            [
                'name' => 'Urban Loft',
                'slug' => 'urban-loft',
                'image' => 'urban-loft.jpg',
                'description' => 'Immerse yourself in city living in our stylish urban lofts. With modern design and convenient amenities, these lofts provide the perfect base for exploring the vibrant energy of the city.',
                'status' => 1,
            ]
        ];

        foreach ($roomTypes as $room) {
            DB::table('room_types')->insert($room);
        }
    }
}
