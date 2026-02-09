<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailCount extends Model
{
    protected $fillable = [
        'email_count',
        'password_count',
        'order_count',
    ];
}
