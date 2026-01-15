<?php

namespace App\Http\Controllers;

use App\Helpers\FileUpload;
use App\Models\Banner;
use App\Models\NewsLetter;
use App\Trait\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    use ApiResponse;

    public function newsLetters()
    {
        return $this->successResponse(NewsLetter::all(), 'News Letter list');
    }

    public function newsLetterStore(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'emails' => 'required|string',
        ]);

        $emails = array_unique(array_map('trim', explode(',', $request->emails)));

        foreach ($emails as $email) {
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'message' => "Invalid email: $email",
                ], 422);
            }
        }

        $newsletter = NewsLetter::create([
            'subject' => $request->subject,
            'content' => $request->content,
            'emails' => $emails,
        ]);

        return $this->successResponse($newsletter, 'News Letter created successfully');
    }

    public function newsLettersView($id)
    {
        $newsletter = NewsLetter::find($id);

        return $this->successResponse($newsletter, 'News Letter details');
    }

    public function index()
    {
        return $this->successResponse(Banner::all(), 'Banners list');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'link' => 'required|url',
            'cpm' => 'required|numeric|min:0',
            'page_name' => 'required|string|max:255',
            'date' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $dates = explode(' - ', $request->date);

        if (count($dates) !== 2) {
            return response()->json(['message' => 'Invalid date range format'], 422);
        }

        try {
            $start_date = Carbon::createFromFormat('M d, Y', trim($dates[0]))->format('Y-m-d');
            $end_date = Carbon::createFromFormat('M d, Y', trim($dates[1]))->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid date format. Use: Jan 15, 2026 - Jan 20, 2026'], 422);
        }

        if ($end_date < $start_date) {
            return response()->json(['message' => 'End date must be after start date'], 422);
        }

        $file = null;
        if ($request->hasFile('image')) {
            $file = FileUpload::storeFile($request->file('image'), 'uploads/banners');
        }

        $banner = Banner::create([
            'name' => $request->name,
            'link' => $request->link,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'cpm' => $request->cpm,
            'page_name' => $request->page_name,
            'image' => $file,
        ]);

        return response()->json([
            'message' => 'Banner created successfully',
            'data' => $banner,
        ]);
    }

    public function edit($id)
    {
        return $this->successResponse(Banner::find($id), 'Banner details');
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'link' => 'required|url',
            'cpm' => 'required|numeric|min:0',
            'page_name' => 'required|string|max:255',
            'date' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $dates = explode(' - ', $request->date);

        if (count($dates) !== 2) {
            return response()->json(['message' => 'Invalid date range format'], 422);
        }

        try {
            $start_date = Carbon::createFromFormat('M d, Y', trim($dates[0]))->format('Y-m-d');
            $end_date = Carbon::createFromFormat('M d, Y', trim($dates[1]))->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid date format. Use: Jan 15, 2026 - Jan 20, 2026'], 422);
        }

        if ($end_date < $start_date) {
            return response()->json(['message' => 'End date must be after start date'], 422);
        }

        if ($request->hasFile('image')) {
            if ($banner->image && file_exists(public_path($banner->image))) {
                unlink(public_path($banner->image));
            }
            $file = FileUpload::storeFile($request->file('image'), 'uploads/banners');
            $banner->image = $file;
        }

        $banner->name = $request->name;
        $banner->link = $request->link;
        $banner->start_date = $start_date;
        $banner->end_date = $end_date;
        $banner->cpm = $request->cpm;
        $banner->page_name = $request->page_name;
        $banner->save();

        return response()->json([
            'message' => 'Banner updated successfully',
            'data' => $banner,
        ]);
    }

    public function view($id)
    {
        return $this->successResponse(Banner::find($id), 'Banner details');
    }

    public function updateStatus($id)
    {
        $banner = Banner::find($id);
        $banner->status = !$banner->status;
        $banner->save();

        return $this->successResponse($banner, "Banner status updated successfully");
    }
}
