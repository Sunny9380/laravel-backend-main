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
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('payment_method', ['online', 'offline'])->default('online')->after('payment_status');
        });
        Schema::table('hotels', function (Blueprint $table) {
            $table->boolean('pay_in_property')->default(0)->after('property_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn('pay_in_property');
        });
    }
};
