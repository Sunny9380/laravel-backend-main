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
        Schema::table('bookings', function (Blueprint $table) {
            $table->integer('room_rate');
            $table->integer('extra_guest_charge');
            $table->integer('platform_fee');
            $table->integer('convenience_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['room_rate', 'extra_guest_charge', 'platform_fee', 'convenience_fee']);
        });
    }
};
