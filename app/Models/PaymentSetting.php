<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = [
        'mollie_key',
        'mollie_webhook',
        'mollie_status',
        'stripe_secret_key',
        'stripe_publishable_key',
        'stripe_webhook',
        'stripe_status',
    ];
}
