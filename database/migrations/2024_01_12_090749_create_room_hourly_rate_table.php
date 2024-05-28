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
        Schema::create('room_hourly_rate', function (Blueprint $table) {
            $table->id();

            $table->foreignId('room_id')->unique();
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');

            $table->boolean('is_percent')->default(true);
            $table->unsignedInteger('_3_hr')->default(50);
            $table->unsignedInteger('_4_hr')->default(50);
            $table->unsignedInteger('_5_hr')->default(60);
            $table->unsignedInteger('_6_hr')->default(65);
            $table->unsignedInteger('_7_hr')->default(65);
            $table->unsignedInteger('_8_hr')->default(75);
            $table->unsignedInteger('_9_hr')->default(75);
            $table->unsignedInteger('_10_hr')->default(75);
            $table->unsignedInteger('_11_hr')->default(85);
            $table->unsignedInteger('_12_hr')->default(85);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_hourly_rate');
    }
};
