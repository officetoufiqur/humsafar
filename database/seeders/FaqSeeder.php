<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Faq::insert([
            [
                'faq_category_id' => 1,
                'question' => 'How do I create an account?',
                'answer' => "Click on 'Add Profile', fill out your details, and your account will be ready within minutes.",
                'status' => true,
            ],
            [
                'faq_category_id' => 1,
                'question' => 'Is the service free to use?',
                'answer' => 'Yes, creating an account and browsing profiles is free. Premium features may be available later.',
                'status' => true,
            ],
            [
                'faq_category_id' => 1,
                'question' => 'Can I edit my profile later?',
                'answer' => 'Absolutely! You can edit your details, photos, and preferences anytime from your dashboard.',
                'status' => true,
            ]
        ]);
    }
}
