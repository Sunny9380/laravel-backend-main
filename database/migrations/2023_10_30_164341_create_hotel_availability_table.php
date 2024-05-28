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
        Schema::create('hotel_availability', function (Blueprint $table) {
            $table->id();
            $table->integer('vendor_id')->foreign('vendor_id')->references('id')->on('vendor')->unique();
            $table->time('time_in')->default(8);
            $table->time('time_out')->default(24);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_availability');
    }
};
