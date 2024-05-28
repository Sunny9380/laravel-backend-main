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
        Schema::create('configuration', function (Blueprint $table) {
            $table->id();
            $table->integer('convenience_fee')->default(80);
            $table->integer('platform_fee')->default(160);
            $table->timestamps();
        });
        \Illuminate\Support\Facades\Artisan::call('db:seed', array('--class' => 'ConfigurationSeeder'));    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuration');
    }
};
