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
        Schema::create('chat_settings', function (Blueprint $table) {
            $table->id();
            $table->string('message_length');
            $table->string('file_size');
            $table->longText('notice');
            $table->string('notice_style');
            $table->string('display_name_formate');
            $table->boolean('enable_image')->default(false);
            $table->boolean('enable_video')->default(false);
            $table->boolean('enable_file')->default(false);
            $table->string('file_extension');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_settings');
    }
};
