<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configuration =
            [
                'convenience_fee' => 80,
                'platform_fee' => 160,
            ];


        DB::table('configuration')->insert($configuration);
    }
}
