<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_id',
        'title',
        'subtitle',
    ];

    public function work()
    {
        return $this->belongsTo(Work::class, 'work_id');
    }
}
