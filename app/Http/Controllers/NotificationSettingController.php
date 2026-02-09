<?php

namespace App\Http\Controllers;

use App\Models\MailCount;
use App\Models\NotificationSetting;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class NotificationSettingController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $email_this_month = MailCount::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('email_count');

        $password_this_month = MailCount::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('password_count');

        $order_this_month = MailCount::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('order_count');

        $notifications = NotificationSetting::where('language', 'en')->get();

        if ($notifications->isEmpty()) {
            return $this->errorResponse('No notification templates found', 404);
        }

        return $this->successResponse([
            'email_count' => $email_this_month,
            'password_count' => $password_this_month,
            'order_count' => $order_this_month,
            'notifications' => $notifications
        ], 'Notifications retrieved successfully');
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

        if (! $notification) {
            return $this->errorResponse('Template not found', 404);
        }

        return $this->successResponse($notification, 'Notification template retrieved successfully');
    }

    public function templateName($id)
    {
        $notification = NotificationSetting::where('id', $id)->select('id', 'template_name')->first();

        if (! $notification) {
            return $this->errorResponse('Template not found', 404);
        }

        return $this->successResponse($notification, 'Notification template retrieved successfully');
    }

    public function update(Request $request)
    {
        $request->validate([
            'template_name' => 'required|string',
            'language' => 'required|string|max:5',
        ]);

        $notification = NotificationSetting::where('template_name', $request->template_name)
            ->where('language', $request->language)
            ->first();

        if (! $notification) {
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
