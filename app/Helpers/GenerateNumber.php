<?php

namespace App\Helpers;

class GenerateNumber
{
    public static function generate(string $prefix, $model, string $column = 'invoice_number')
    {
        $last = $model::where($column, 'like', $prefix.'-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $parts = explode('-', $last->$column);
            $lastNumber = intval(end($parts));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $prefix.'-'.$newNumber;
    }
}

