<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_length',
        'file_size',
        'notice',
        'notice_style',
        'display_name_formate',
        'enable_image',
        'enable_video',
        'enable_file',
        'file_extension',
    ];
}