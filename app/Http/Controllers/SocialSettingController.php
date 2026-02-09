<?php

namespace App\Http\Controllers;

use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use App\Models\SocialSetting;

class SocialSettingController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $socialSetting = SocialSetting::first();

        return $this->successResponse($socialSetting);
    }

    public function update(Request $request)
    {
        $socialSetting = SocialSetting::first();

        if (!$socialSetting) {
            return $this->errorResponse('Social settings not found.', 404);
        }

        $socialSetting->google_login_enabled = $request->google_login_enabled ?? $socialSetting->google_login_enabled;
        $socialSetting->google_client_id = $request->google_client_id ?? $socialSetting->google_client_id;
        $socialSetting->google_client_secret = $request->google_client_secret ?? $socialSetting->google_client_secret;

        $socialSetting->facebook_login_enabled = $request->facebook_login_enabled ?? $socialSetting->facebook_login_enabled;
        $socialSetting->facebook_client_id = $request->facebook_client_id ?? $socialSetting->facebook_client_id;
        $socialSetting->facebook_client_secret = $request->facebook_client_secret ?? $socialSetting->facebook_client_secret;
        $socialSetting->save();

        return $this->successResponse($socialSetting, 'Social settings updated successfully.');
    }

    public function updateRecaptcha(Request $request)
    {
        $socialSetting = SocialSetting::first();

        if (!$socialSetting) {
            return $this->errorResponse('Social settings not found.', 404);
        }

        $socialSetting->recaptcha_enabled = $request->recaptcha_enabled ?? $socialSetting->recaptcha_enabled;
        $socialSetting->recaptcha_site_key = $request->recaptcha_site_key ?? $socialSetting->recaptcha_site_key;
        $socialSetting->recaptcha_secret_key = $request->recaptcha_secret_key ?? $socialSetting->recaptcha_secret_key;

        $socialSetting->save();

        return $this->successResponse($socialSetting, 'Social settings updated successfully.');
    }
}
