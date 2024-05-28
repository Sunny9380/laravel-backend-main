<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            [
                'id' => 1,
                'user_id' => 1,
                'vendor_id' => 1,
                'email' => 'jk@gmail.com',
                'name' => 'Jammu Hotels',
                'address' => 'Jammu Hotels in Jammu',
                'phone_number' => '12345678',
                'gst_number' => '1231231232',
                'is_active' => 1,
                'created_at' => '2024-01-20 19:43:19',
                'updated_at' => '2024-03-04 18:04:13',
                'razorpay_id' => 'acc_23213jh12k3jhDFF33',
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'vendor_id' => 2,
                'email' => 'vermamanav110@gmail.com',
                'name' => 'SUPER Hotels',
                'address' => 'super hotels at super place',
                'phone_number' => '12345678',
                'gst_number' => '23432432',
                'is_active' => 0,
                'created_at' => '2024-02-21 19:58:58',
                'updated_at' => '2024-02-21 19:58:58',
                'razorpay_id' => null,
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'vendor_id' => 3,
                'email' => 'manavverma.me@gmail.com',
                'name' => 'Twitter Hotels',
                'address' => 'asdkhasd',
                'phone_number' => '1231232',
                'gst_number' => 'askdhkahsd',
                'is_active' => 1,
                'created_at' => '2024-02-21 21:16:15',
                'updated_at' => '2024-02-21 21:16:15',
                'razorpay_id' => null,
            ],
            [
                'id' => 4,
                'user_id' => 4,
                'vendor_id' => 4,
                'email' => 'vendor@example.com',
                'name' => 'Vendor XYZ',
                'address' => '123 Street, City',
                'phone_number' => '9876543210',
                'gst_number' => 'GST1234567',
                'is_active' => 1,
                'created_at' => '2024-05-01 10:00:00',
                'updated_at' => '2024-05-01 10:00:00',
                'razorpay_id' => 'acc_1234567890',
            ],
        ];

        foreach ($vendors as $vendor) {
            DB::table('vendor')->insert($vendor);
        }
    }
}
