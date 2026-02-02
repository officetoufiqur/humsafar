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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->unique();
            $table->foreignId('package_id')->nullable()->constrained('packages')->cascadeOnDelete();
            $table->string('gender')->nullable();
            $table->integer('age')->nullable();
            $table->string('origin')->nullable();
            $table->string('looking_for')->nullable();
            $table->string('relationship')->nullable();
            $table->string('children')->nullable();
            $table->string('religion')->nullable();
            $table->string('location')->nullable();
            $table->string('hair_color')->nullable();
            $table->string('eye_color')->nullable();
            $table->string('body_type')->nullable();
            $table->string('appearance')->nullable();
            $table->string('intelligence')->nullable();
            $table->string('clothing')->nullable();
            $table->string('mother_tongue')->nullable();
            $table->string('known_language')->nullable();
            $table->string('language')->nullable();
            $table->string('weight')->nullable(); 
            $table->string('height')->nullable();
            $table->string('education')->nullable();
            $table->string('career')->nullable();
            $table->longText('about_me')->nullable();
            $table->json('sports')->nullable();
            $table->json('music')->nullable();
            $table->json('cooking')->nullable();
            $table->json('reading')->nullable();
            $table->json('tv_shows')->nullable();
            $table->json('personal_attitude')->nullable();
            $table->string('smoke')->nullable();
            $table->string('drinking')->nullable();
            $table->string('going_out')->nullable();
            $table->string('edu_primary')->nullable();
            $table->string('edu_secondary')->nullable();
            $table->string('edu_qualification')->nullable();
            $table->string('experience')->nullable();
            $table->string('certifications')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->string('exercise')->nullable();
            $table->string('diet')->nullable();
            $table->string('pets')->nullable();
            $table->string('travel')->nullable();
            $table->string('social_media')->nullable();
            $table->string('work_life_balance')->nullable();
            $table->string('night_life')->nullable();
            $table->string('hobby')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
