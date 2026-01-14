<?php

namespace Database\Seeders;

use App\Models\ProfileAttribute;
use Illuminate\Database\Seeder;

class ProfileAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['label' => 'Origin', 'values' => json_encode(['demo', 'test']), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Religion', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Relationship', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Children', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Education', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Eye Color', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Hair Color', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Body Type', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Appearance', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Clothing Styles', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Intelligence', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Character Traits', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Sports', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Hobbies', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Music', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Kitchen', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => "I'm Looking for a", 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Career', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Known Languages', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Reading', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'TV Shows', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Lengte', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Languages', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Diploma', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Mother Tongue', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Personal Attitude', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Cast', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Sub-Cast', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => "I'm a", 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Smoke', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Drinking', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Going Out', 'values' => json_encode([]), 'showOn' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        // Insert into database
        ProfileAttribute::insert($data);

    }
}
