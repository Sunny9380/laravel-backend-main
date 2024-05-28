<?php

namespace Database\Seeders;

use App\Models\Blogs;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogs = [
            [
                'title' => 'Top 10 Must-Visit Destinations in Europe',
                'meta_title' => 'Must-Visit Destinations in Europe',
                'slug' => 'top-10-must-visit-destinations-europe',
                'image' => 'https://example.com/images/europe.jpg',
                'meta_keywords' => 'Europe, travel, destinations, must-visit',
                'body' => 'Europe is a continent full of diverse cultures, historical landmarks, and breathtaking natural scenery...',
                'is_active' => 1,
                'views' => 1200,
            ],
            [
                'title' => 'A Guide to Backpacking Through Southeast Asia',
                'meta_title' => 'Backpacking Through Southeast Asia',
                'slug' => 'guide-backpacking-southeast-asia',
                'image' => 'https://example.com/images/southeast-asia.jpg',
                'meta_keywords' => 'Southeast Asia, backpacking, travel guide',
                'body' => 'Southeast Asia offers some of the best backpacking experiences, with its rich cultures, affordable prices, and stunning landscapes...',
                'is_active' => 1,
                'views' => 950,
            ],
            [
                'title' => 'Exploring the Wonders of the Grand Canyon',
                'meta_title' => 'Grand Canyon Wonders',
                'slug' => 'exploring-grand-canyon-wonders',
                'image' => 'https://example.com/images/grand-canyon.jpg',
                'meta_keywords' => 'Grand Canyon, travel, adventure, nature',
                'body' => 'The Grand Canyon is one of the most iconic natural landmarks in the United States, offering spectacular views and thrilling adventures...',
                'is_active' => 1,
                'views' => 1300,
            ],
            [
                'title' => 'Discover the Beauty of the Swiss Alps',
                'meta_title' => 'Beauty of the Swiss Alps',
                'slug' => 'discover-swiss-alps-beauty',
                'image' => 'https://example.com/images/swiss-alps.jpg',
                'meta_keywords' => 'Swiss Alps, travel, mountains, skiing',
                'body' => 'The Swiss Alps are renowned for their stunning beauty, offering a paradise for skiers, hikers, and nature enthusiasts...',
                'is_active' => 1,
                'views' => 1100,
            ],
            [
                'title' => 'A Culinary Journey Through Italy',
                'meta_title' => 'Culinary Journey Italy',
                'slug' => 'culinary-journey-italy',
                'image' => 'https://example.com/images/italy.jpg',
                'meta_keywords' => 'Italy, culinary, food, travel',
                'body' => 'Italy is a food lover’s dream, with its rich culinary traditions, delicious dishes, and vibrant food markets...',
                'is_active' => 1,
                'views' => 1400,
            ],
            [
                'title' => 'Safari Adventures in the African Savannah',
                'meta_title' => 'Safari Adventures Africa',
                'slug' => 'safari-adventures-africa',
                'image' => 'https://example.com/images/africa-safari.jpg',
                'meta_keywords' => 'Africa, safari, travel, wildlife',
                'body' => 'Experience the thrill of a lifetime with a safari adventure in the African savannah, where you can see incredible wildlife up close...',
                'is_active' => 1,
                'views' => 1500,
            ],
            [
                'title' => 'The Best Beaches in the Caribbean',
                'meta_title' => 'Best Beaches Caribbean',
                'slug' => 'best-beaches-caribbean',
                'image' => 'https://example.com/images/caribbean-beaches.jpg',
                'meta_keywords' => 'Caribbean, beaches, travel, tropical',
                'body' => 'The Caribbean is home to some of the world’s most beautiful beaches, with their crystal-clear waters and pristine sands...',
                'is_active' => 1,
                'views' => 1600,
            ],
            [
                'title' => 'Exploring Ancient Ruins in South America',
                'meta_title' => 'Ancient Ruins South America',
                'slug' => 'exploring-ancient-ruins-south-america',
                'image' => 'https://example.com/images/south-america-ruins.jpg',
                'meta_keywords' => 'South America, ruins, history, travel',
                'body' => 'South America is rich in history, with numerous ancient ruins that tell the stories of past civilizations...',
                'is_active' => 1,
                'views' => 1700,
            ],
            [
                'title' => 'A Road Trip Through the Australian Outback',
                'meta_title' => 'Road Trip Australian Outback',
                'slug' => 'road-trip-australian-outback',
                'image' => 'https://example.com/images/australian-outback.jpg',
                'meta_keywords' => 'Australia, road trip, outback, travel',
                'body' => 'The Australian Outback offers a unique road trip experience, with its vast landscapes, unique wildlife, and remote beauty...',
                'is_active' => 1,
                'views' => 1800,
            ],
            [
                'title' => 'Cultural Experiences in Japan',
                'meta_title' => 'Cultural Experiences Japan',
                'slug' => 'cultural-experiences-japan',
                'image' => 'https://example.com/images/japan.jpg',
                'meta_keywords' => 'Japan, culture, travel, experiences',
                'body' => 'Japan offers a fascinating blend of ancient traditions and modern innovation, making it a must-visit destination for cultural experiences...',
                'is_active' => 1,
                'views' => 1900,
            ],
        ];

        foreach ($blogs as $blog) {
            Blogs::create($blog);
        }
    }
}
