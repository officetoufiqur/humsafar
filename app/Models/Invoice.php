<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_id',
        'package_id',
        'invoice_number',
        'invoice_date',
        'description',
        'total_amount',
        'paid_amount',
        'remaining_amount',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
