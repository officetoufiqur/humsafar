<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterSetting extends Model
{
    protected $fillable = [
        'footer_logo',
        'footer_description',
        'footer_link',
        'footer_search_name',
        'footer_contact',
    ];
}
