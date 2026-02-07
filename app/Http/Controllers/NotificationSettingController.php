<?php

namespace App\Http\Controllers;

use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use App\Models\NotificationSetting;

class NotificationSettingController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $notifications = NotificationSetting::where('language', 'en')->get();

        if ($notifications->isEmpty()) {
            return $this->errorResponse('No notification templates found', 404);
        }

        return $this->successResponse($notifications, 'Notifications retrieved successfully');
    }
    
    public function edit(Request $request)
    {
        $request->validate([
            'template_name' => 'required|string',
            'language' => 'required|string|max:5',
        ]);

        $notification = NotificationSetting::where('template_name', $request->template_name)
            ->where('language', $request->language)
            ->first();

        if (!$notification) {
            return $this->errorResponse('Template not found', 404);
        }

        return $this->successResponse($notification, 'Notification template retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'template_name' => 'required|string',
            'language' => 'required|string|max:5',
        ]);

        $notification = NotificationSetting::where('id', $id)
            ->where('template_name', $request->template_name)
            ->where('language', $request->language)
            ->first();

        if (!$notification) {
            return $this->errorResponse('Template not found', 404);
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|boolean',
        ]);

        $notification->subject = $request->subject;
        $notification->content = $request->content;
        $notification->status = $request->status;
        $notification->save();

        return $this->successResponse($notification, 'Notification template updated successfully');
    }
}
