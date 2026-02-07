<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::insert([
            'system_name' => 'Humsafar',
            'system_logo' => null,
            'date_format' => 'Y-m-d',
            'admin_title' => "Here's what's happening today.",
            'member_prefix' => 'MEM',
            'minimum_age' => 18,
            'login_background' => null,
            'welcome_message' => 'Welcome to Humsafar!',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
