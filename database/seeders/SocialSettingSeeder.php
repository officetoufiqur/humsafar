<?php

namespace Database\Seeders;

use App\Models\SocialSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocialSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SocialSetting::insert([
            [
                'google_login_enabled' => false,
                'google_client_id' => "Vg02CdQWhn8pscHlaKpViRisCPBmISpHB45w78jqVzoVlkqGE8w7V2kkzvNrgKYQIkxMG5TGPgWSCibRY3uS00d2tNxpqL",
                'google_client_secret' => "Vg02CdQWhn8pscHlaKpViRisCPBmISpHB45w78jqVzoVlkqGE8w7V2kkzvNrgKYQIkxMG5TGPgWSCibRY3uS00d2tNxpqL",
                'facebook_login_enabled' => false,
                'facebook_client_id' => "Vg02CdQWhn8pscHlaKpViRisCPBmISpHB45w78jqVzoVlkqGE8w7V2kkzvNrgKYQIkxMG5TGPgWSCibRY3uS00d2tNxpqL",
                'facebook_client_secret' => "Vg02CdQWhn8pscHlaKpViRisCPBmISpHB45w78jqVzoVlkqGE8w7V2kkzvNrgKYQIkxMG5TGPgWSCibRY3uS00d2tNxpqL",
                'recaptcha_enabled' => false,
                'recaptcha_site_key' => "Vg02CdQWhn8pscHlaKpViRisCPBmISpHB45w78jqVzoVlkqGE8w7V2kkzvNrgKYQIkxMG5TGPgWSCibRY3uS00d2tNxpqL",
                'recaptcha_secret_key' => "Vg02CdQWhn8pscHlaKpViRisCPBmISpHB45w78jqVzoVlkqGE8w7V2kkzvNrgKYQIkxMG5TGPgWSCibRY3uS00d2tNxpqL",
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
