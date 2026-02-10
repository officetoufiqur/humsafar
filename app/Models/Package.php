<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'validity',
        'description',
        'image',
        'features',
        'status',
        'symbol',
        'currency',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
