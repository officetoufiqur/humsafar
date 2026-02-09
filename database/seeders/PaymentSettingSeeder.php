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
            'mollie_key' => "test_b38t7gVvjqFBz7sQM932N2SQpRTwNs",
            'mollie_webhook' => null,
            'mollie_status' => false,
            'stripe_secret_key' => "pk_test_51SuTVg02CdQWhn8pscHlaKpViRisCPBmISpHB45w78jqVzoVlkqGE8w7V2kkzvNrgKYQIkxMG5TGPgWSCibRY3uS00d2tNxpqL",
            'stripe_publishable_key' => "sk_test_51SuTVg02CdQWhn8pdckrtLHNfyaejXXDi2hvS5fJxv6LwV2pmWTJAK9AMaSH8lLb9qSdbpruHbUZvHACdFV0xFeq00e91lSsCD",
            'stripe_webhook' => "whsec_cc2eb2c547437981186ea18efe0331b77ced5794cea61acbeabad89dda6fedd2",
            'stripe_status' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}