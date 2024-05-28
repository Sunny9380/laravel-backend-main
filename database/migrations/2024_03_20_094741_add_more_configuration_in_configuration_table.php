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
        Schema::table('configuration', function (Blueprint $table) {
            $table->dropColumn('convenience_fee');
            $table->dropColumn('platform_fee');
            $table->dropColumn('razorpay_id');
            $table->string('razorpay_key')->nullable()->after('id');
            $table->string('razorpay_secret')->nullable()->after('razorpay_key');
            $table->string('logo')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configuration', function (Blueprint $table) {
            $table->integer('convenience_fee')->default(0)->after('id');
            $table->integer('platform_fee')->default(0)->after('convenience_fee');
            $table->string('razorpay_id')->nullable()->after('id');
            $table->dropColumn('razorpay_key');
            $table->dropColumn('razorpay_secret');
            $table->dropColumn('logo');
        });
    }
};
