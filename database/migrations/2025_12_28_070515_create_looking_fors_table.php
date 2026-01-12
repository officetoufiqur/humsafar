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
            $table->string('origin')->nullable();
            $table->string('gender')->nullable();
            $table->string('age_range')->nullable();
            $table->integer('height')->nullable(); 
            $table->integer('weight')->nullable();
            $table->string('religion')->nullable();
            $table->string('relationship')->nullable();
            $table->string('education')->nullable();
            $table->string('rook')->nullable();
            $table->string('drinking')->nullable();
            $table->string('going_out')->nullable();
            $table->unsignedTinyInteger('children')->nullable();
            $table->string('location')->nullable();
            $table->string('smoke')->nullable();
            $table->unsignedSmallInteger('distance_km')->nullable();

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
