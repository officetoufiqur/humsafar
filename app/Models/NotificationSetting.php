<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'template_name',
        'subject',
        'content',
        'status',
        'language',
    ];
}
