<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'link',
        'start_date',
        'end_date',
        'cpm',
        'page_name',
        'image',
        'status',
    ];
}
