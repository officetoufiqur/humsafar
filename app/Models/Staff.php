<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Staff extends Authenticatable
{
    use HasFactory, HasRoles;

    protected $guard_name = 'web';

    protected $fillable = [
        'fname',
        'lname',
        'email',
        'phone',
        'password',
        'photo',
        'status',
    ];
}
