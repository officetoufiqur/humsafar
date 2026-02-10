<?php

namespace App\Http\Controllers;

use App\Helpers\FileUpload;
use App\Models\Currencie;
use App\Models\Language;
use App\Models\Package;
use App\Models\Setting;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $setting = Setting::first();

        if (! $setting) {
            return $this->errorResponse('Settings not found', 404);
        }

        return $this->successResponse($setting, 'Settings retrieved successfully');
    }

    public function update(Request $request)
    {
        $setting = Setting::first();

        if (! $setting) {
            return $this->errorResponse('Settings not found', 404);
        }

        $request->validate([
            'system_name' => 'nullable|string|max:255',
            'system_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'date_format' => 'nullable|string|max:255',
            'admin_title' => 'nullable|string|max:255',
            'member_prefix' => 'nullable|string|max:255',
            'minimum_age' => 'nullable|integer|min:0',
            'login_background' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'welcome_message' => 'nullable|string',
        ]);

        if ($request->hasFile('system_logo')) {
            $logoPath = FileUpload::updateFile($request->file('system_logo'), 'logos', $setting->system_logo);
            $setting->system_logo = $logoPath;
        }

        if ($request->hasFile('login_background')) {
            $backgroundPath = FileUpload::updateFile($request->file('login_background'), 'backgrounds', $setting->login_background);
            $setting->login_background = $backgroundPath;
        }

        $setting->system_name = $request->system_name;
        $setting->date_format = $request->date_format;
        $setting->admin_title = $request->admin_title;
        $setting->member_prefix = $request->member_prefix;
        $setting->minimum_age = $request->minimum_age;
        $setting->welcome_message = $request->welcome_message;

        $setting->maintenance_mode = $request->maintenance_mode ?? $setting->maintenance_mode;
        $setting->default_currency = $request->default_currency ?? $setting->default_currency;
        $setting->default_language = $request->default_language ?? $setting->default_language;

        $setting->save();

        $currency = Currencie::where('symbol', $request->default_currency)->first();

        Package::query()->update([
            'symbol' => $currency->symbol,
            'currency' => $currency->code,
        ]);

        return $this->successResponse($setting, 'Settings updated successfully');
    }

    public function currencyData()
    {
        $currencies = Currencie::get();

        $languages = Language::select('code', 'name')->get();

        return $this->successResponse([
            'currencies' => $currencies,
            'languages' => $languages,
        ], 'Currencies and languages retrieved successfully');
    }
}
