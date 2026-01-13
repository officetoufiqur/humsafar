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
        Schema::create('looking_fors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('looking_origin')->nullable();
            $table->string('looking_gender')->nullable();
            $table->string('looking_age_range')->nullable();
            $table->integer('looking_height')->nullable(); 
            $table->integer('looking_weight')->nullable();
            $table->string('looking_religion')->nullable();
            $table->string('looking_relationship')->nullable();
            $table->string('looking_education')->nullable();
            $table->string('looking_rook')->nullable();
            $table->string('looking_drinking')->nullable();
            $table->string('looking_going_out')->nullable();
            $table->unsignedTinyInteger('looking_children')->nullable();
            $table->string('looking_location')->nullable();
            $table->string('looking_smoke')->nullable();
            $table->unsignedSmallInteger('looking_distance_km')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('looking_fors');
    }
};
