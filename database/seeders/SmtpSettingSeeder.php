<?php

namespace Database\Seeders;

use App\Models\SmtpSetting;
use Illuminate\Database\Seeder;

class SmtpSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SmtpSetting::insert([
            'mail_host' => 'sandbox.smtp.mailtrap.io',
            'mail_port' => 2525,
            'mail_username' => 'b38b2b2b38b2b38b2b38b2b38b2b38b2',
            'mail_password' => 'b38b2b2b38b2b38b2b38b2b38b2b38b2',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'humsafar@gmail.com',
            'mail_from_name' => 'Humsafar',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
