<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDashboardSetting extends Model
{
    protected $fillable = [
        'page',
    ];

    protected $casts = [
        'page' => 'array',
    ];
}
