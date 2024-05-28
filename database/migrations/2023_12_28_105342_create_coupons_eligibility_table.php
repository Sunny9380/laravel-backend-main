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
        Schema::create('coupons_eligibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons')->onDelete('cascade');
            $table->integer('price_range_from')->nullable();
            $table->integer('price_range_to')->nullable();
            $table->boolean('is_new_user_eligible')->default(0);
            $table->boolean('is_all_users_eligible')->default(1);
            $table->boolean('is_price_valid')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons_eligibility');
    }
};
