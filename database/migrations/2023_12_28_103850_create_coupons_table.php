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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->integer('limit_per_use')->default(1);
            $table->boolean('is_discount_in_percent')->default(0);
            $table->integer('discount')->default(0);
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_till')->nullable();
            $table->integer('num_of_coupons')->default(0)->nullable();
            $table->longText('conditions')->nullable();
            $table->string('source_type');
            $table->foreignId('source_id')->constrained('users');
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
