<?php

namespace Database\Seeders;

use App\Models\FooterSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FooterSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FooterSetting::insert([
            'footer_logo' => null,
            'footer_description' => "All you singles, listen up! If you're looking to fall in love, want to start dating, ready to start a relationship, or want to keep it casual, you need to be on Humsafar. With over 55 billion matches made, it's the best place to find your next best match. You've probably noticed, the dating landscape looks very different today, with most of us choosing to meet people online. With humsafar, the world's most popular free dating app, you have millions of other singles at your fingertips, and they're all ready to meet someone like you. Whether you're straight or a part of the experience, Humsafar got you covered. The right swipe gets flying.There really is something for everyone on Humsafar. Looking for a relationship? You've got it. Want to make friends online? Say no more. Just started uni and want to make the most of your experience? Humsafar got you covered. Humsafar isn't your average dating site - it's the most diverse dating app, where adults of all backgrounds and experiences are invited to make connections, memories and everything in between.",
            'footer_link' => "Safety tips,Terms,Cookie Policy,Privacy settings",
            'footer_search_name' => null,
            'footer_contact' => "© 2025 Humsafar L.L.C. All Rights Reserved",
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
