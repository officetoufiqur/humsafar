<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'subject',
        'priority',
        'status',
        'category',
        'description',
        'reply',
    ];

    public function replies()
    {
        return $this->hasMany(TicketReplay::class);
    }
}