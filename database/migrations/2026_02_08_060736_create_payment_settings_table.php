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
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('mollie_key')->nullable();
            $table->string('mollie_webhook')->nullable();
            $table->boolean('mollie_status')->default(false);
            $table->string('stripe_secret_key')->nullable();
            $table->string('stripe_publishable_key')->nullable();
            $table->string('stripe_webhook')->nullable();
            $table->boolean('stripe_status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
