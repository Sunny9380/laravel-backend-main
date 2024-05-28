<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hotel_type_booking_options', function (Blueprint $table) {
            $table->id();
            $table->integer('hotel_id')->foreign('hotel_id')->references('id')->on('hotels');
            $table->integer('booking_type_id')->foreign('booking_type_id')->references('id')->on('booking_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_type_booking_options');
    }
};
