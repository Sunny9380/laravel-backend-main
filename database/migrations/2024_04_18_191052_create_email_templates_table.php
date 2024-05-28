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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->longText('body');
            $table->string('attachments')->nullable();
            $table->enum('type', ['welcome', 'booking-success', 'cancellation', 'thanks-for-review',    'booking-reminder', 'booking-confirmation', 'booking-confirmation-vendor', 'booking-reminder-vendor', 'booking-reminder-customer', 'booking-confirmation-customer'])->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        \Illuminate\Support\Facades\Artisan::call('db:seed', array('--class' => 'EmailTemplatesSeeder'));

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
