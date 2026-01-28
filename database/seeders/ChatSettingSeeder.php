<?php

namespace Database\Seeders;

use App\Models\ChatSetting;
use Illuminate\Database\Seeder;

class ChatSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ChatSetting::insert([
            [
                'message_length' => '150',
                'file_size' => '2',
                'notice' => 'Humsafar chat center',
                'notice_style' => 'Banner',
                'display_name_formate' => 'Full name',
                'file_extension' => 'doc,txt,docx,pdf,jpg,png,jpeg',
            ],
        ]);
    }
}
