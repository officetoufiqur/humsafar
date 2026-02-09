<?php

namespace App\Http\Controllers;

use App\Trait\ApiResponse;
use App\Helpers\FileUpload;
use Illuminate\Http\Request;
use App\Models\FooterSetting;
use App\Models\UserDashboardSetting;

class FooterSettingController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $footerSettings = FooterSetting::first();

        return $this->successResponse($footerSettings);
    }

    public function update(Request $request)
    {
        $footerSettings = FooterSetting::first();

        if (!$footerSettings) {
            $footerSettings = new FooterSetting();
        }

        if ($request->hasFile('footer_logo')) {
            $file = FileUpload::updateFile($request->file('footer_logo'), 'footer_logos');
            $footerSettings->footer_logo = $file;
        }

        $footerSettings->footer_description = $request->footer_description;
        $footerSettings->footer_link = $request->footer_link;
        $footerSettings->footer_search_name = $request->footer_search_name;
        $footerSettings->footer_contact = $request->footer_contact;
        
        $footerSettings->save();

        return $this->successResponse($footerSettings, 'Footer settings updated successfully');
    }

    public function userDashboardSettings()
    {
        $userDashboard = UserDashboardSetting::first();

        return $this->successResponse($userDashboard, 'User dashboard settings retrieved successfully');
    }

    public function updateUserDashboardSettings(Request $request)
    {
        $request->validate([
            'page' => 'required|array',
        ]);

        $userDashboard = UserDashboardSetting::first();

        if (!$userDashboard) {
            $userDashboard = new UserDashboardSetting();
        }

        $userDashboard->page = $request->page;
        $userDashboard->save();

        return $this->successResponse($userDashboard, 'User dashboard settings updated successfully');
    }
}
