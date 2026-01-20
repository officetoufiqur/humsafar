<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrontendSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_name',
        'slug',
        'url',
        'content',
        'is_active',
    ];

    protected $casts = [
        'content' => 'array',
    ];
}
