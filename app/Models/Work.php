<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    protected $fillable = [
        'heading',
        'subheading',
        'step',
        'title',
        'subtitle',
        'image',
    ];

    public function steps()
    {
        return $this->hasMany(WorkStep::class);
    }
}
