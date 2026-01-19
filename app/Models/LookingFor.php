<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LookingFor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'looking_gender',
        'looking_origin',
        'looking_relationship',
        'looking_religion',
        'looking_age_range',
        'looking_height',
        'looking_weight',
        'looking_education',
        'looking_childern',
        'looking_smoke',
        'looking_drinking',
        'looking_going_out',
        'looking_location',
        'looking_distance_km',
        'looking_country',
        'looking_state',
        'looking_city'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
