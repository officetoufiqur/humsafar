<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyFormate extends Model
{
    protected $table = 'currency_formates';

    protected $fillable = [
        'currency_id',
        'symbol_format',
        'decimal_separator',
        'decimal_places',
    ];

    public function currency()
    {
        return $this->belongsTo(Currencie::class);
    }
}
