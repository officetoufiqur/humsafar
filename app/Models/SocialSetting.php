<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialSetting extends Model
{
    protected $fillable = [
        'google_login_enabled',
        'google_client_id',
        'google_client_secret',
        'facebook_login_enabled',
        'facebook_client_id',
        'facebook_client_secret',
        'recaptcha_enabled',
        'recaptcha_site_key',
        'recaptcha_secret_key',
    ];
}