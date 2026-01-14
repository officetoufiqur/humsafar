<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'values',
        'showOn',
    ];

    protected $casts = [
        'values' => 'array',
        'showOn' => 'boolean',
    ];
}
