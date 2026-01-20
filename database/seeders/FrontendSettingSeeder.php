<?php

namespace Database\Seeders;

use App\Models\FrontendSetting;
use Illuminate\Database\Seeder;

class FrontendSettingSeeder extends Seeder
{
    public function run(): void
    {
        FrontendSetting::insert([
            [
                'page_name' => 'Home',
                'slug' => 'home',
                'content' => json_encode(['title' => 'Home Page']),
                'url' => 'https://ticketprijs.nl',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page_name' => 'Contact',
                'slug' => 'contact',
                'content' => json_encode(['title' => 'Contact Page']),
                'url' => 'https://ticketprijs.nl/contact',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page_name' => 'How Work',
                'slug' => 'how-work',
                'content' => json_encode(['title' => 'How Work Page']),
                'url' => 'https://ticketprijs.nl/how-work',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page_name' => 'Registration',
                'slug' => 'registration',
                'content' => json_encode(['title' => 'Registration Page']),
                'url' => 'https://ticketprijs.nl/profile-create',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page_name' => 'Term and conditions',
                'slug' => 'term-and-conditions',
                'content' => json_encode(['title' => 'Term and conditions Page']),
                'url' => 'https://ticketprijs.nl/terms',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page_name' => 'Veelgestelde Vragen',
                'slug' => 'veelgestelde-vragen',
                'content' => json_encode(['title' => 'Veelgestelde Vragen Page']),
                'url' => 'https://ticketprijs.nl/veelgestelde-vragen',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page_name' => 'Agenda',
                'slug' => 'agenda',
                'content' => json_encode(['title' => 'Agenda Page']),
                'url' => 'https://ticketprijs.nl/agenda',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
