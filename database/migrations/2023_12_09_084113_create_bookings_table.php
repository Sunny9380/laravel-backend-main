<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_id');
            $table->string('user_id');
            $table->string('hotel_id');
            $table->string('order_id')->nullable();
            $table->string('booking_type');
            $table->string('name');
            $table->string('guest_name')->nullable();
            $table->string('amount');
            $table->string('email');
            $table->string('primary_phone_number');
            $table->string('secondary_phone_number')->nullable();
            $table->text('notes');
            $table->json('rooms'); //later it is deleted and changed to integer contains id of booked rooms table
            $table->dateTime('check_in');
            $table->dateTime('check_out')->nullable();
            $table->string('check_in_hours')->nullable();
            $table->time('check_in_time')->nullable();
            $table->string('razorpay_payment_id')->nullable();
            $table->string('razorpay_signature')->nullable();
            $table->enum('payment_status', ['pending', 'Paid'])->default('pending');
            $table->boolean('is_cancelled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
