<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            [
                'name' => 'Hotel A',
                'image' => 'hotel_a.jpg',
                'is_stopped' => 0,
            ],
            [
                'name' => 'Hotel B',
                'image' => 'hotel_b.jpg',
                'is_stopped' => 1,
            ],
            [
                'name' => 'Hotel C',
                'image' => 'hotel_c.jpg',
                'is_stopped' => 0,
            ],
            [
                'name' => 'Hotel D',
                'image' => 'hotel_d.jpg',
                'is_stopped' => 0,
            ],
            [
                'name' => 'Hotel E',
                'image' => 'hotel_e.jpg',
                'is_stopped' => 1,
            ],
        ];

        foreach ($states as $state) {
            DB::table('states')->insert($state);
        }
    }
}
