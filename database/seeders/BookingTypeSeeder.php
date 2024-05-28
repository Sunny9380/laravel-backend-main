<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            [
                'name' => 'Overnight',
                'description' => 'Book a hotel or other accommodation for just the night you need, at a fraction of the cost of a traditional hotel stay. Choose from a full night stay (between 12 and 24 hours) and enjoy all the amenities of the hotel.',
                'MinTime' => 1,
                'MaxTime' => 30,
            ],
            [
                'name' => 'Hourly',
                'description' => 'Book a hotel or other accommodation for just the time you need, at a fraction of the cost of a traditional hotel stay. Choose from a microstay (between 3 and 6 hours) or a standard stay (between 6 and 24 hours) and enjoy all the amenities of the hotel.',
                'MinTime' => 3,
                'MaxTime' => 12,
            ],
            [
                'name' => 'Weekend Retreat',
                'description' => 'Escape to a peaceful weekend retreat and rejuvenate your mind and body. Choose from cozy cottages, scenic cabins, or luxurious resorts nestled in nature.',
                'MinTime' => 2,
                'MaxTime' => 3,
            ],
            [
                'name' => 'Adventure Expedition',
                'description' => 'Embark on an adrenaline-fueled adventure expedition in breathtaking destinations. Experience thrilling activities such as hiking, mountain biking, and zip-lining.',
                'MinTime' => 3,
                'MaxTime' => 7,
            ],
            [
                'name' => 'Cultural Exploration',
                'description' => 'Immerse yourself in the rich culture and history of vibrant cities and charming towns. Discover iconic landmarks, museums, and local cuisine.',
                'MinTime' => 2,
                'MaxTime' => 5,
            ],
            [
                'name' => 'Wellness Retreat',
                'description' => 'Nourish your body and soul with a wellness retreat focused on holistic healing and relaxation. Indulge in yoga sessions, spa treatments, and nutritious meals.',
                'MinTime' => 3,
                'MaxTime' => 7,
            ],
            [
                'name' => 'Beach Getaway',
                'description' => 'Soothe your senses with a tranquil beach getaway. Lounge on pristine sandy beaches, swim in crystal-clear waters, and enjoy stunning sunset views.',
                'MinTime' => 2,
                'MaxTime' => 5,
            ],
            [
                'name' => 'Skiing Adventure',
                'description' => 'Hit the slopes and experience the thrill of skiing in picturesque mountain resorts. Enjoy breathtaking views, fresh powder snow, and après-ski activities.',
                'MinTime' => 2,
                'MaxTime' => 4,
            ],
            [
                'name' => 'Romantic Escape',
                'description' => 'Celebrate love and romance with a dreamy getaway for two. Enjoy intimate candlelit dinners, sunset strolls, and luxurious accommodations.',
                'MinTime' => 2,
                'MaxTime' => 4,
            ],
            [
                'name' => 'Solo Travel Experience',
                'description' => 'Embark on a solo travel adventure and discover new destinations at your own pace. Meet fellow travelers, immerse yourself in local culture, and gain valuable insights.',
                'MinTime' => 1,
                'MaxTime' => 7,
            ],
            [
                'name' => 'Historical Journey',
                'description' => 'Step back in time and explore the wonders of ancient civilizations and historical landmarks. Visit UNESCO World Heritage sites, monuments, and archaeological sites.',
                'MinTime' => 3,
                'MaxTime' => 7,
            ],
            [
                'name' => 'Foodie Expedition',
                'description' => 'Embark on a culinary adventure and tantalize your taste buds with local delicacies and gourmet cuisine. Experience food tours, cooking classes, and gastronomic delights.',
                'MinTime' => 2,
                'MaxTime' => 5,
            ],
        ];

        foreach ($activities as $activity) {
            DB::table('booking_types')->insert($activity);
        }
    }
}
