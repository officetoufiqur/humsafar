<?php

namespace App\Http\Controllers;

use App\Helpers\FileUpload;
use App\Models\FrontendSetting;
use App\Models\Seo;
use App\Models\User;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontendSettingController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $frontendSettings = FrontendSetting::get();

        return $this->successResponse($frontendSettings, 'Frontend Settings');
    }

    public function edit(Request $request, $id)
    {
        $frontendSetting = FrontendSetting::with('seo')->find($id);

        if (! $frontendSetting) {
            return $this->errorResponse('Frontend Setting not found', 404);
        }

        return $this->successResponse($frontendSetting, 'Frontend Setting');
    }

    public function store(Request $request)
    {
        $request->validate([
            'page_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'content' => 'required|array',
            'meta_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $slug = str_replace(' ', '-', strtolower($request->page_name));

        $content = $request->content;
        $content['title'] = $request->title;

        $frontendSetting = new FrontendSetting;
        $frontendSetting->page_name = $request->page_name;
        $frontendSetting->slug = $slug;
        $frontendSetting->url = $request->url;

        $frontendSetting->content = $content;

        $frontendSetting->save();

        if ($request->hasFile('meta_image')) {
            $imagePath = FileUpload::storeFile($request->file('meta_image'), 'uploads/frontend');
        }

        Seo::create([
            'frontend_id' => $frontendSetting->id,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'page_type' => $request->page_type,
            'meta_image' => $imagePath,
        ]);

        return $this->successResponse($frontendSetting, 'Frontend Setting');
    }

    public function update(Request $request, $id)
    {
        $frontendSetting = FrontendSetting::findOrFail($id);
        $slug = $request->query('slug');

        if ($slug === 'home') {
            $request->validate([
                'title' => 'nullable|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'faq_title' => 'nullable|string|max:255',
                'faq_subtitle' => 'nullable|string|max:255',
                'faq_description' => 'nullable|string',
                'faq_title1' => 'nullable|string|max:255',
                'faq_title2' => 'nullable|string|max:255',
                'faq_title3' => 'nullable|string|max:255',
                'faq_subtitle1' => 'nullable|string|max:255',
                'faq_subtitle2' => 'nullable|string|max:255',
                'faq_subtitle3' => 'nullable|string|max:255',
                'dating_title' => 'nullable|string|max:255',
                'dating_subtitle' => 'nullable|string|max:255',
                'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'dating_image1' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'dating_image2' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'dating_image3' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'dating_image4' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            $existingData = is_array($frontendSetting->content)
                ? $frontendSetting->content
                : json_decode($frontendSetting->content, true);

            $bannerImage = $existingData['banner_image'] ?? null;
            $datingImage1 = $existingData['dating_image1'] ?? null;
            $datingImage2 = $existingData['dating_image2'] ?? null;
            $datingImage3 = $existingData['dating_image3'] ?? null;
            $datingImage4 = $existingData['dating_image4'] ?? null;

            if ($request->hasFile('banner_image')) {
                $bannerImage = FileUpload::updateFile($request->file('banner_image'), 'uploads/frontend_settings', $bannerImage);
            }

            if ($request->hasFile('dating_image1')) {
                $datingImage1 = FileUpload::updateFile($request->file('dating_image1'), 'uploads/frontend_settings', $datingImage1);
            }

            if ($request->hasFile('dating_image2')) {
                $datingImage2 = FileUpload::updateFile($request->file('dating_image2'), 'uploads/frontend_settings', $datingImage2);
            }

            if ($request->hasFile('dating_image3')) {
                $datingImage3 = FileUpload::updateFile($request->file('dating_image3'), 'uploads/frontend_settings', $datingImage3);
            }

            if ($request->hasFile('dating_image4')) {
                $datingImage4 = FileUpload::updateFile($request->file('dating_image4'), 'uploads/frontend_settings', $datingImage4);
            }

            $data = [
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'banner_image' => $bannerImage,
                'faq_title' => $request->faq_title,
                'faq_subtitle' => $request->faq_subtitle,
                'faq_description' => $request->faq_description,
                'faq_title1' => $request->faq_title1,
                'faq_title2' => $request->faq_title2,
                'faq_title3' => $request->faq_title3,
                'faq_subtitle1' => $request->faq_subtitle1,
                'faq_subtitle2' => $request->faq_subtitle2,
                'faq_subtitle3' => $request->faq_subtitle3,
                'dating_title' => $request->dating_title,
                'dating_subtitle' => $request->dating_subtitle,
                'dating_image1' => $datingImage1,
                'dating_image2' => $datingImage2,
                'dating_image3' => $datingImage3,
                'dating_image4' => $datingImage4,
            ];

            $frontendSetting->content = $data;
            $frontendSetting->save();

            return $this->successResponse(
                $frontendSetting,
                'Frontend Setting'
            );

        } elseif ($slug === 'contact') {
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
        } elseif ($slug === 'how-works') {
            $request->validate([
                'work_title' => 'nullable|string',
                'title' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'description' => 'nullable|string',
                'step' => 'nullable|string',
                'step_title' => 'nullable|string',
                'step_subtitle' => 'nullable|string',
                'step_description' => 'nullable|string',
                'step_title1' => 'nullable|string',
                'step_title2' => 'nullable|string',
                'step_title3' => 'nullable|string',
                'step_subtitle1' => 'nullable|string',
                'step_subtitle2' => 'nullable|string',
                'step_subtitle3' => 'nullable|string',
                'faq_title1' => 'nullable|string',
                'faq_title2' => 'nullable|string',
                'faq_title3' => 'nullable|string',
                'faq_title4' => 'nullable|string',
                'faq_value1' => 'nullable|string',
                'faq_value2' => 'nullable|string',
                'faq_value3' => 'nullable|string',
                'faq_value4' => 'nullable|string',
            ]);

            $existingData = is_array($frontendSetting->content)
                ? $frontendSetting->content
                : json_decode($frontendSetting->content, true);

            $imagePath = $existingData['image'] ?? null;

            if ($request->hasFile('image')) {
                $imagePath = FileUpload::updateFile($request->file('image'), 'uploads/frontend_settings', $imagePath);
            }

            $data = [
                'work_title' => $request->work_title,
                'title' => $request->title,
                'image' => $imagePath,
                'description' => $request->description,
                'step' => $request->step,
                'step_title' => $request->step_title,
                'step_subtitle' => $request->step_subtitle,
                'step_description' => $request->step_description,
                'step_title1' => $request->step_title1,
                'step_title2' => $request->step_title2,
                'step_title3' => $request->step_title3,
                'step_subtitle1' => $request->step_subtitle1,
                'step_subtitle2' => $request->step_subtitle2,
                'step_subtitle3' => $request->step_subtitle3,
                'faq_title1' => $request->faq_title1,
                'faq_title2' => $request->faq_title2,
                'faq_title3' => $request->faq_title3,
                'faq_title4' => $request->faq_title4,
                'faq_value1' => $request->faq_value1,
                'faq_value2' => $request->faq_value2,
                'faq_value3' => $request->faq_value3,
                'faq_value4' => $request->faq_value4,
            ];

            $frontendSetting->content = $data;
            $frontendSetting->save();

            return $this->successResponse($frontendSetting, 'How It Works Settings Updated Successfully');

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

            $bannerImage = $frontendSetting->banner_image;

            if ($request->hasFile('banner_image')) {
                $bannerImage = FileUpload::updateFile($request->file('banner_image'), 'uploads/frontend_settings', $frontendSetting->banner_image);
            }

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
                'banner_image' => $bannerImage,
            ];

            $frontendSetting->content = $data;
            $frontendSetting->save();

            return $this->successResponse($frontendSetting, 'Registration Settings Updated Successfully');
        }elseif ($slug === $frontendSetting->slug) {
            $request->validate([
                'title' => 'required|string'
            ]);

            $data = [
                'body' => $request->content,
                'title' => $request->title,
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
        }

        return $this->errorResponse('Invalid Slug', 400);
    }

    public function howItWorks(Request $request)
    {
        $slug = $request->query('slug');

        $frontendSetting = FrontendSetting::where('slug', $slug)->first();

        if (! $frontendSetting) {
            return $this->errorResponse('Invalid Slug', 400);
        }

        return $this->successResponse($frontendSetting, 'Page data');
    }

    public function dating()
    {
        $user = Auth::user();

        $query = User::with('profile:id,user_id,location')
            ->select('id', 'fname', 'lname', 'photo')
            ->latest();

        if ($user) {
            $query->where('id', '!=', $user->id);
        }

        $users = $query->take(4)->get();

        return $this->successResponse($users, 'Dating');
    }

    public function destroy($id)
    {
        $frontendSetting = FrontendSetting::find($id);

        if (! $frontendSetting) {
            return $this->errorResponse('Frontend Setting not found', 404);
        }

        $frontendSetting->delete();

        return $this->successResponse($frontendSetting, 'Frontend Setting deleted successfully');
    }
}
