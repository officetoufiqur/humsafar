<?php

namespace App\Http\Controllers;

use App\Models\Currencie;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use App\Models\CurrencyFormate;

class CurrencieController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $currencies = Currencie::get();

        if ($currencies->isEmpty()) {
            return $this->errorResponse('No currencies found', 404);
        }

        return $this->successResponse($currencies, 'Currencies retrieved successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|unique:currencies,code',
            'symbol' => 'required|string',
            'text_direction' => 'required|string|in:ltr,rtl',
        ]);

        $currency  = Currencie::create([
            'name' => $request->name,
            'code' => $request->code,
            'symbol' => $request->symbol,
            'text_direction' => $request->text_direction,
        ]);

        $formate = CurrencyFormate::exists();
        if (!$formate) {
            CurrencyFormate::create([
                'currency_id' => $currency->id,
                'symbol_format' => 'before_amount',
                'decimal_separator' => '.',
                'decimal_places' => 2,
            ]);
        }

        return $this->successResponse($currency, 'Currency created successfully');
    }

    public function update(Request $request, $id)
    {
        $currencie = Currencie::find($id);

        if (!$currencie) {
            return $this->errorResponse('Currency not found', 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string',
            'code' => 'sometimes|required|string|unique:currencies,code,' . $id,
            'symbol' => 'sometimes|required|string',
            'text_direction' => 'sometimes|required|string|in:ltr,rtl',
        ]);

        $currencie->name = $request->name ?? $currencie->name;
        $currencie->code = $request->code ?? $currencie->code;
        $currencie->symbol = $request->symbol ?? $currencie->symbol;
        $currencie->text_direction = $request->text_direction ?? $currencie->text_direction;
        $currencie->save();

        return $this->successResponse($currencie, 'Currency updated successfully');
    }

    public function updateFormate(Request $request)
    {
        $request->validate([
            'currency_id' => 'required|exists:currencies,id',
            'symbol_format' => 'required|in:before_amount,after_amount',
            'decimal_separator' => 'required',
            'decimal_places' => 'required|integer|min:0',
        ]);

        $currencyFormate = CurrencyFormate::first();
        if (!$currencyFormate) {
            return $this->errorResponse('Currency format not found', 404);
        }

        $currencyFormate->currency_id = $request->currency_id;
        $currencyFormate->symbol_format = $request->symbol_format;
        $currencyFormate->decimal_separator = $request->decimal_separator;
        $currencyFormate->decimal_places = $request->decimal_places;
        $currencyFormate->save();

        return $this->successResponse($currencyFormate, 'Currency format updated successfully');
    }

    public function destroy($id)
    {
        $currencie = Currencie::find($id);

        if (!$currencie) {
            return $this->errorResponse('Currency not found', 404);
        }

        $currencie->delete();

        return $this->successResponse(null, 'Currency deleted successfully');
    }
}
