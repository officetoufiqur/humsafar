<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'reported_by',
        'reported_to',
        'reason',
        'additional_details',
        'status',
    ];

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function reportedTo()
    {
        return $this->belongsTo(User::class, 'reported_to');
    }

    public function replies()
    {
        return $this->hasMany(ComplaintReply::class);
    }
}
