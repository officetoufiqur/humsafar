<?php

namespace App\Http\Controllers;

use App\Models\ChatSetting;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class ChatSettingController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $chat = ChatSetting::first();

        if (! $chat) {
            return $this->errorResponse('Chat setting not found.', 404);
        }

        return $this->successResponse($chat, 'Chat setting retrieved successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'message_length' => 'required|string',
            'file_size' => 'required|string',
            'notice' => 'required|string',
            'notice_style' => 'required|string',
            'display_name_formate' => 'required|string',
            'file_extension' => 'required|string',
            'enable_image' => 'nullable|boolean',
            'enable_video' => 'nullable|boolean',
            'enable_file' => 'nullable|boolean',
        ]);

        $chat = ChatSetting::updateOrCreate(
            ['id' => 1],
            [
                'message_length' => $request->message_length,
                'file_size' => $request->file_size,
                'notice' => $request->notice,
                'notice_style' => $request->notice_style,
                'display_name_formate' => $request->display_name_formate,
                'enable_image' => $request->enable_image ?? false,
                'enable_video' => $request->enable_video ?? false,
                'enable_file' => $request->enable_file ?? false,
                'file_extension' => $request->file_extension,
            ]
        );

        return $this->successResponse($chat, 'Chat setting saved successfully.');
    }
}
