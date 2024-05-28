<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ratings = [
            [
                'user_id' => 1,
                'hotel_id' => 1,
                'rating' => 4,
                'review' => 'Great experience overall. The hotel staff was very friendly and accommodating. The room was clean and comfortable.',
                'status' => 1,
            ],
            [
                'user_id' => 2,
                'hotel_id' => 2,
                'rating' => 5,
                'review' => 'Absolutely fantastic! The hotel exceeded all expectations. From the luxurious amenities to the breathtaking views, everything was perfect.',
                'status' => 1,
            ],
            [
                'user_id' => 3,
                'hotel_id' => 3,
                'rating' => 3,
                'review' => 'Decent hotel for the price. The room was a bit outdated, but it served its purpose for a short stay.',
                'status' => 1,
            ],
            [
                'user_id' => 4,
                'hotel_id' => 4,
                'rating' => 5,
                'review' => 'Exquisite hotel with impeccable service. The attention to detail was remarkable. Can\'t wait to return!',
                'status' => 1,
            ],
        ];

        foreach ($ratings as $rating) {
            DB::table('ratings')->insert($rating);
        }
    }
}
