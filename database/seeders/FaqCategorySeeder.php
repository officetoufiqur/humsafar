<?php

namespace Database\Seeders;

use App\Models\FaqCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FaqCategory::insert([
            [
                'name' => 'General',
                'slug' => 'general',
                'status' => true,
            ],
            [
                'name' => 'Billing',
                'slug' => 'billing',
                'status' => true,
            ],
            [
                'name' => 'Technical Support',
                'slug' => 'technical-support',
                'status' => true,
            ]
        ]);
    }
}
