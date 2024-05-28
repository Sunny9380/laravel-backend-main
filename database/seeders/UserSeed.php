<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'google_id' => null,
                'image' => 'john.jpg',
                'address' => '123 Main Street, Anytown',
                'phone_number' => '123-456-7890',
                'dob' => '1990-05-15',
                'gender' => 'Male',
                'is_blocked' => 0,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'google_id' => null,
                'image' => 'jane.jpg',
                'address' => '456 Elm Street, Anytown',
                'phone_number' => '987-654-3210',
                'dob' => '1992-08-20',
                'gender' => 'Female',
                'is_blocked' => 0,
            ],
            [
                'id' => 3,
                'name' => 'Manav Verma',
                'email' => 'vermamanav117@gmail.com',
                'password' => Hash::make('password'), // Hashed password
                'google_id' => '107210210447804208575',
                'image' => '65f7609805d14_OIP (37).jpeg',
                'address' => 'House in Jammu',
                'phone_number' => '1231231231',
                'dob' => '2002-12-13',
                'gender' => 'male',
                'is_blocked' => 0,
                'created_at' => '2024-01-20 19:41:59',
                'updated_at' => '2024-03-20 20:58:00',
                'role' => 2,
            ],
            [
                'id' => 4,
                'name' => 'Jammu Hotels',
                'email' => 'jk@gmail.com',
                'password' => Hash::make('password'),
                'address' => 'Jammu Hotels in Jammu',
                'phone_number' => '12345678',
                'created_at' => '2024-01-20 19:43:19',
                'updated_at' => '2024-05-09 20:04:45',
                'role' => 1,
            ],
            [
                'id' => 5,
                'name' => 'Test User',
                'email' => 'abc@gmail.com',
                'password' => Hash::make('password'),
                'address' => 'abc near test',
                'phone_number' => '1231231232',
                'dob' => '2024-01-04',
                'gender' => 'male',
                'created_at' => '2024-01-27 19:04:25',
                'updated_at' => '2024-05-09 20:10:02',
                'role' => 0,
            ],
            [
                'id' => 25,
                'name' => 'SUPER Hotels',
                'email' => 'vermamanav110@gmail.com',
                'password' => Hash::make('password'),
                'address' => 'super hotels at super place',
                'phone_number' => '12345678',
                'created_at' => '2024-02-20 10:15:41',
                'updated_at' => '2024-02-21 19:58:58',
                'role' => 1,
            ],
            [
                'id' => 26,
                'name' => 'Manav',
                'email' => 'manavverma.me@gmail.com',
                'password' => Hash::make('password'),
                'created_at' => '2024-02-21 20:37:12',
                'updated_at' => '2024-05-09 20:10:41',
                'role' => 1,
            ],

        ];

        foreach ($users as $user) {
            DB::table('users')->insert($user);
        }
    }
}
