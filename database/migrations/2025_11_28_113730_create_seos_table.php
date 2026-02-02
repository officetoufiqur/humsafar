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
        Schema::create('seos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('frontend_id')->nullable()->constrained('frontend_settings')->nullOnDelete();
            $table->string('meta_title');
            $table->text('meta_description');
            $table->string('meta_keywords');
            $table->string('meta_image')->nullable();
            $table->string('page_type')->nullable();
            $table->boolean('show_header')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seos');
    }
};
