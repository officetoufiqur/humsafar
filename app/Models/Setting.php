<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'system_name',
        'system_logo',
        'date_format',
        'admin_title',
        'member_prefix',
        'minimum_age',
        'login_background',
        'welcome_message',
        'maintenance_mode',
        'default_currency',
        'default_language',
    ];
}