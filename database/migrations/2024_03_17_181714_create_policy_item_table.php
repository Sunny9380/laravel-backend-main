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
        Schema::create('policy_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_id')->constrained('policies')->cascadeOnDelete();
            $table->text('policy');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        //dropping policies column from poilicies table
        Schema::table('policies', function (Blueprint $table) {
            $table->dropColumn('policies');
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_item');

        //adding policies column to poilicies table
        Schema::table('policies', function (Blueprint $table) {
            $table->text('policies');
            $table->dropColumn('is_active');
        });
    }
};
