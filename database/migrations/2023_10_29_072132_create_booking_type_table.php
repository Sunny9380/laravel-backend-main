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
        Schema::create('booking_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->integer('MinTime')->default(3);
            $table->integer('MaxTime')->default(24);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        \Illuminate\Support\Facades\Artisan::call('db:seed', array('--class' => 'BookingTypeSeeder'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_types');
    }
};
