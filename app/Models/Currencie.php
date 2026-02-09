<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currencie extends Model
{
    protected $table = 'currencies';

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'text_direction',
    ];

    public function currencyFormate()
    {
        return $this->hasOne(CurrencyFormate::class);
    }
}
