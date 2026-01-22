<?php

namespace App\Http\Controllers;

use App\Helpers\FileUpload;
use App\Models\FrontendSetting;
use App\Models\Seo;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class FrontendSettingController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $frontendSettings = FrontendSetting::select('id', 'page_name', 'slug', 'url', 'is_active')->get();

        return $this->successResponse($frontendSettings, 'Frontend Settings');
    }

    public function edit(Request $request, $id)
    {
        $frontendSetting = FrontendSetting::find($id);

        $slug = $request->query('slug');

        return $this->successResponse($frontendSetting, 'Frontend Setting');
    }

    public function update(Request $request, $id)
    {
        $frontendSetting = FrontendSetting::findOrFail($id);
        $slug = $request->query('slug');

        if ($slug === 'contact') {
            $request->validate([
                'contact_name' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'title' => 'nullable|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'address_name' => 'nullable|string',
                'address_value' => 'nullable|string',
                'phone_name' => 'nullable|string',
                'phone_value' => 'nullable|string',
                'email_name' => 'nullable|string',
                'email_value' => 'nullable|email',
                'form_title' => 'nullable|string',
                'form_description' => 'nullable|string',
            ]);

            $existingData = is_array($frontendSetting->content)
                ? $frontendSetting->content
                : json_decode($frontendSetting->content, true);

            $imagePath = $existingData['image'] ?? null;

            if ($request->hasFile('image')) {
                $imagePath = FileUpload::updateFile($request->file('image'), 'uploads/frontend_settings', $imagePath);
            }

            $data = [
                'contact_name' => $request->contact_name,
                'image' => $imagePath,
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'description' => $request->description,
                'address_name' => $request->address_name,
                'address_value' => $request->address_value,
                'phone_name' => $request->phone_name,
                'phone_value' => $request->phone_value,
                'email_name' => $request->email_name,
                'email_value' => $request->email_value,
                'form_title' => $request->form_title,
                'form_description' => $request->form_description,
            ];

            $frontendSetting->content = $data;
            $frontendSetting->save();

            return $this->successResponse($frontendSetting, 'Contact Settings Updated Successfully');
        } elseif ($slug === 'terms-and-conditions') {
            $request->validate([
                'title' => 'required|string',
                'link' => 'required|string',
                'content' => 'required|string',
            ]);

            $data = [
                'title' => $request->title,
                'link' => $request->link,
                'content' => $request->content,
            ];

            $frontendSetting->content = $data;
            $frontendSetting->save();

            $seo = Seo::where('frontend_id', $frontendSetting->id)->first();

            if ($seo) {
                $seo->meta_title = $request->meta_title;
                $seo->meta_description = $request->meta_description;
                $seo->meta_keywords = $request->meta_keywords;
                $seo->page_type = $request->page_type;
                $seo->show_header = $request->show_header;
                if ($request->hasFile('meta_image')) {
                    $imagePath = FileUpload::updateFile($request->file('meta_image'), 'uploads/seos', $seo->meta_image);
                    $seo->meta_image = $imagePath;
                }
                $seo->save();
            } else {
                if ($request->hasFile('meta_image')) {
                    $imagePath = FileUpload::storeFile($request->file('meta_image'), 'uploads/seos');
                }

                Seo::create([
                    'frontend_id' => $frontendSetting->id,
                    'meta_title' => $request->meta_title,
                    'meta_description' => $request->meta_description,
                    'meta_keywords' => $request->meta_keywords,
                    'page_type' => $request->page_type,
                    'show_header' => $request->show_header,
                    'meta_image' => $imagePath,
                ]);

            }

            return $this->successResponse($frontendSetting, 'Term and Conditions Updated Successfully');
        } elseif ($slug === 'registration') {
            $request->validate([
                'create' => 'required|string',
                'details' => 'required|string',
                'description' => 'required|string',
                'personal_info' => 'required|string',
                'my_image' => 'required|string',
                'my_description' => 'required|string',
                'partner' => 'required|string',
                'profile' => 'required|string',
                'varification' => 'required|string',
                'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            $data = [
                'create' => $request->create,
                'details' => $request->details,
                'description' => $request->description,
                'personal_info' => $request->personal_info,
                'my_image' => $request->my_image,
                'my_description' => $request->my_description,
                'partner' => $request->partner,
                'profile' => $request->profile,
                'varification' => $request->varification,
            ];

            if ($request->hasFile('banner_image')) {
                $imagePath = FileUpload::updateFile($request->file('banner_image'), 'uploads/frontend_settings', $frontendSetting->banner_image);
                $frontendSetting->banner_image = $imagePath;
            }
            $frontendSetting->content = $data;
            $frontendSetting->save();

            return $this->successResponse($frontendSetting, 'Registration Settings Updated Successfully');
        }

        return $this->errorResponse('Invalid Slug', 400);
    }
}
