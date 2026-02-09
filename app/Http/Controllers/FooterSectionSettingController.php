<?php

namespace App\Http\Controllers;

use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use App\Models\FooterSectionSetting;

class FooterSectionSettingController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $footerSectionSettings = FooterSectionSetting::all();

        return $this->successResponse($footerSectionSettings);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'page' => 'required|string',
        ]);

        $footerSectionSetting = FooterSectionSetting::create([
            'name' => $request->name,
            'page' => $request->page,
        ]);

        return $this->successResponse($footerSectionSetting, 'Footer section setting created successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'page' => 'required|string',
        ]);

        $footerSectionSetting = FooterSectionSetting::findOrFail($id);
        $footerSectionSetting->name = $request->name;
        $footerSectionSetting->page = $request->page;
        $footerSectionSetting->save();

        return $this->successResponse($footerSectionSetting, 'Footer section setting updated successfully');
    }

    public function destroy($id)
    {
        $footerSectionSetting = FooterSectionSetting::findOrFail($id);
        $footerSectionSetting->delete();

        return $this->successResponse(null, 'Footer section setting deleted successfully');
    }
}
