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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendor');
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->foreignId('property_type_id')->constrained('property_types');
            $table->string("primary_number");
            $table->string("secondary_number");
            $table->string("primary_email");
            $table->string("secondary_email");
            $table->text('address');
            $table->foreignId('city_id')->constrained('cities');
            $table->string('country');
            $table->string('zip');
            $table->text('location_iframe');
            $table->string('banner_image');
            $table->string('tenancy_agreement');
            $table->string('corporate_documents');
            $table->string('identity_documents');
            $table->string('proof_of_ownership');
            $table->boolean('isActive')->default(false);
            $table->boolean('isVerified')->default(false);
            $table->boolean('isBanned')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
