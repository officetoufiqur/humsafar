<?php

namespace Database\Seeders;

use App\Models\PaymentSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentSetting::insert([
            'mollie_key' => "mollie_test_key",
            'mollie_webhook' => null,
            'mollie_status' => false,
            'stripe_secret_key' => "stripe_test_secret_key",
            'stripe_publishable_key' => "stripe_test_publishable_key",
            'stripe_webhook' => "stripe_test_webhook",
            'stripe_status' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
    }
}