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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('display_name')->nullable();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->date('dob')->nullable();
            $table->string('photo')->nullable();
            $table->string('email');
            $table->string('password');
            $table->string('otp')->nullable();
            $table->boolean('is_accept')->default(0);
            $table->boolean('is_complete')->default(0);
            $table->boolean('is_permission')->default(0);
            $table->timestamp('is_online')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('language')->nullable();
            $table->boolean('members_with_photo')->nullable()->default(0);
            $table->boolean('vip_members')->nullable()->default(0);
            $table->boolean('blur_photo')->nullable()->default(0);
            $table->boolean('members_send_request')->nullable()->default(0);
            $table->enum('status',['active','inactive','blocked','unblocked'])->default('active');
            $table->enum('membership_type',['free','vip'])->default('free');
            $table->dateTime('vip_expires_at')->nullable();
            $table->dateTime('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
