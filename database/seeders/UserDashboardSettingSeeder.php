<?php

namespace Database\Seeders;

use App\Models\UserDashboardSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserDashboardSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserDashboardSetting::insert([
            'page' => json_encode([
                'About',
                'Contact'
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
