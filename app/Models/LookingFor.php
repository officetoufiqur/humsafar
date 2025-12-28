<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LookingFor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender',
        'origin',
        'relationship',
        'religion',
        'age_range',
        'height',
        'weight',
        'education',
        'childern',
        'smoke',
        'drinking',
        'going_out',
        'location',
        'distance_km',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
