<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            [
                'subject' => 'Welcome!',
                'body' => 'Welcome to our platform! We are excited to have you on board.',
                'type' => 'welcome',
                'is_active' => true
            ],
            [
                'subject' => 'Booking Success!',
                'body' => 'Your booking has been successfully placed. We hope you have a great experience!',
                'type' => 'booking-success',
                'is_active' => true
            ],
            [
                'subject' => 'Booking Cancellation',
                'body' => 'Your booking has been cancelled. We hope to see you again soon!',
                'type' => 'cancellation',
                'is_active' => true
            ],
            [
                'subject' => 'Thanks for your review!',
                'body' => 'Thank you for your review. We appreciate your feedback!',
                'type' => 'thanks-for-review',
                'is_active' => true
            ],
            [
                'subject' => 'Booking Reminder',
                'body' => 'This is a reminder for your upcoming booking. We hope you have a great experience!',
                'type' => 'booking-reminder',
                'is_active' => true
            ],
            [
                'subject' => 'Booking Confirmation',
                'body' => 'Your booking has been confirmed. We hope you have a great experience!',
                'type' => 'booking-confirmation',
                'is_active' => true
            ],
            [
                'subject' => 'Booking Confirmation Vendor',
                'body' => 'A booking has been confirmed. We hope you have a great experience!',
                'type' => 'booking-confirmation-vendor',
                'is_active' => true
            ],
            [
                'subject' => 'Booking Reminder Vendor',
                'body' => 'This is a reminder for an upcoming booking. We hope you have a great experience!',
                'type' => 'booking-reminder-vendor',
                'is_active' => true
            ],
            [
                'subject' => 'Booking Reminder Customer',
                'body' => 'This is a reminder for your upcoming booking. We hope you have a great experience!',
                'type' => 'booking-reminder-customer',
                'is_active' => true
            ],
            [
                'subject' => 'Booking Confirmation Customer',
                'body' => 'Your booking has been confirmed. We hope you have a great experience!',
                'type' => 'booking-confirmation-customer',
                'is_active' => true
            ]
        ];

        foreach ($activities as $activity) {
            DB::table('email_templates')->insert($activity);
        }
    }
}
