<?php

namespace Database\Seeders;

use App\Models\NotificationSetting;
use Illuminate\Database\Seeder;

class NotificationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NotificationSetting::insert([
            [
                'template_name' => 'welcome_email',
                'subject' => 'Welcome to Humsafar!',
                'content' => 'Hello {name}, welcome to Humsafar! We are excited to have you on board.',
                'status' => true,
                'language' => 'en',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'template_name' => 'welcome_email',
                'subject' => 'Welcome to Humsafar!',
                'content' => 'Hello {name}, welcome to Humsafar! We are excited to have you on board.',
                'status' => true,
                'language' => 'sp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'template_name' => 'welcome_email',
                'subject' => 'Welcome to Humsafar!',
                'content' => 'Hello {name}, welcome to Humsafar! We are excited to have you on board.',
                'status' => true,
                'language' => 'fr',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'template_name' => 'welcome_email',
                'subject' => 'Welcome to Humsafar!',
                'content' => 'Hello {name}, welcome to Humsafar! We are excited to have you on board.',
                'status' => true,
                'language' => 'ge',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'template_name' => 'welcome_email',
                'subject' => 'Welcome to Humsafar!',
                'content' => 'Hello {name}, welcome to Humsafar! We are excited to have you on board.',
                'status' => true,
                'language' => 'it',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'template_name' => 'password_reset',
                'subject' => 'Reset Your Password',
                'content' => 'Hello {name}, click the link below to reset your password: {reset_link}',
                'status' => true,
                'language' => 'en',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'template_name' => 'password_reset',
                'subject' => 'Reset Your Password',
                'content' => 'Hello {name}, click the link below to reset your password: {reset_link}',
                'status' => true,
                'language' => 'sp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'template_name' => 'password_reset',
                'subject' => 'Reset Your Password',
                'content' => 'Hello {name}, click the link below to reset your password: {reset_link}',
                'status' => true,
                'language' => 'fr',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'template_name' => 'password_reset',
                'subject' => 'Reset Your Password',
                'content' => 'Hello {name}, click the link below to reset your password: {reset_link}',
                'status' => true,
                'language' => 'ge',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'template_name' => 'password_reset',
                'subject' => 'Reset Your Password',
                'content' => 'Hello {name}, click the link below to reset your password: {reset_link}',
                'status' => true,
                'language' => 'it',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'template_name' => 'order',
                'subject' => 'Your Order Confirmation',
                'content' => 'Hello {name}, your order #{order_id} has been placed successfully.',
                'status' => true,
                'language' => 'en',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'template_name' => 'order',
                'subject' => 'Your Order Confirmation',
                'content' => 'Hello {name}, your order #{order_id} has been placed successfully.',
                'status' => true,
                'language' => 'sp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'template_name' => 'order',
                'subject' => 'Your Order Confirmation',
                'content' => 'Hello {name}, your order #{order_id} has been placed successfully.',
                'status' => true,
                'language' => 'fr',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'template_name' => 'order',
                'subject' => 'Your Order Confirmation',
                'content' => 'Hello {name}, your order #{order_id} has been placed successfully.',
                'status' => true,
                'language' => 'ge',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'template_name' => 'order',
                'subject' => 'Your Order Confirmation',
                'content' => 'Hello {name}, your order #{order_id} has been placed successfully.',
                'status' => true,
                'language' => 'it',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
