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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('system_name')->nullable();
            $table->string('system_logo')->nullable();
            $table->string('date_format')->default('Y-m-d');
            $table->string('admin_title')->nullable();
            $table->string('member_prefix', 20)->nullable();
            $table->integer('minimum_age')->nullable();
            $table->string('login_background')->nullable();
            $table->text('welcome_message')->nullable();
            $table->boolean('maintenance_mode')->default(false);
            $table->string('default_currency')->default('USD');
            $table->string('default_language')->default('en');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
