<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            [
                'state_id' => 1,
                'name' => 'California',
                'slug' => 'california',
                'image' => 'california.jpg',
                'is_stopped' => 1,
            ],
            [
                'state_id' => 2,
                'name' => 'New York',
                'slug' => 'new-york',
                'image' => 'new-york.jpg',
                'is_stopped' => 1,
            ],
            [
                'state_id' => 3,
                'name' => 'Texas',
                'slug' => 'texas',
                'image' => 'texas.jpg',
                'is_stopped' => 1,
            ],
            [
                'state_id' => 4,
                'name' => 'Florida',
                'slug' => 'florida',
                'image' => 'florida.jpg',
                'is_stopped' => 1,
            ],
            [
                'state_id' => 5,
                'name' => 'Illinois',
                'slug' => 'illinois',
                'image' => 'illinois.jpg',
                'is_stopped' => 1,
            ],
            [
                'state_id' => 1,
                'name' => 'Ohio',
                'slug' => 'ohio',
                'image' => 'ohio.jpg',
                'is_stopped' => 1,
            ],
            [
                'state_id' => 2,
                'name' => 'Michigan',
                'slug' => 'michigan',
                'image' => 'michigan.jpg',
                'is_stopped' => 1,
            ],
            [
                'state_id' => 3,
                'name' => 'Colorado',
                'slug' => 'colorado',
                'image' => 'colorado.jpg',
                'is_stopped' => 1,
            ],
            [
                'state_id' => 4,
                'name' => 'Arizona',
                'slug' => 'arizona',
                'image' => 'arizona.jpg',
                'is_stopped' => 1,
            ],
            [
                'state_id' => 5,
                'name' => 'Washington',
                'slug' => 'washington',
                'image' => 'washington.jpg',
                'is_stopped' => 1,
            ],
            [
                'state_id' => 2,
                'name' => 'Oregon',
                'slug' => 'oregon',
                'image' => 'oregon.jpg',
                'is_stopped' => 1,
            ],
            [
                'state_id' => 1,
                'name' => 'Nevada',
                'slug' => 'nevada',
                'image' => 'nevada.jpg',
                'is_stopped' => 1,
            ],
            [
                'state_id' => 2,
                'name' => 'Georgia',
                'slug' => 'georgia',
                'image' => 'georgia.jpg',
                'is_stopped' => 1,
            ],
        ];

        foreach ($cities as $city) {
            DB::table('cities')->insert($city);
        }
    }
}
