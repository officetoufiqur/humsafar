<?php

namespace App\Http\Controllers\Api;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Controller;
use App\Models\ChatSetting;
use App\Models\Message;
use App\Models\User;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    use ApiResponse;

    public function getChatUsers()
    {
        $authId = Auth::id();

        $users = User::with('profile:id,user_id,age,origin,location,gender,about_me,eye_color,hair_color,body_type,religion,relationship,state')->where('id', '!=', $authId)
            ->whereIn('id', function ($query) use ($authId) {
                $query->selectRaw("
                CASE 
                    WHEN sender_id = $authId THEN receiver_id
                    ELSE sender_id
                END
            ")
                    ->from('messages')
                    ->where(function ($q) use ($authId) {
                        $q->where('sender_id', $authId)
                            ->orWhere('receiver_id', $authId);
                    });
            })
            ->get();

        $users->map(function ($user) use ($authId) {

            $user->unread_count = Message::where('sender_id', $user->id)
                ->where('receiver_id', $authId)
                ->whereNull('read_at')
                ->count();

            $user->last_message = Message::where(function ($q) use ($authId, $user) {
                $q->where('sender_id', $authId)->where('receiver_id', $user->id);
            })
                ->orWhere(function ($q) use ($authId, $user) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $authId);
                })
                ->latest()
                ->first();
            
            $user->is_online = $user->isOnline();

            return $user;
        });

        $users = $users->sortByDesc(function ($user) {
            return optional($user->last_message)->created_at;
        })->values();

        return $this->successResponse($users, 'Chat users with last message');
    }

    public function messages($id)
    {
        $authId = Auth::id();

        Message::where('sender_id', $id)
            ->where('receiver_id', $authId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::select('id', 'sender_id', 'receiver_id', 'message', 'created_at', 'read_at')
            ->with([
                'sender:id,fname,lname,photo',
                'sender.profile:id,user_id,age,location,gender,about_me,eye_color,hair_color,body_type,religion,relationship,state',
                'receiver:id,fname,lname,photo',
                'receiver.profile:id,user_id,age,location,gender,about_me,eye_color,hair_color,body_type,religion,relationship,state',
            ])
            ->where(function ($q) use ($authId, $id) {
                $q->where('sender_id', $authId)
                    ->where('receiver_id', $id);
            })
            ->orWhere(function ($q) use ($authId, $id) {
                $q->where('sender_id', $id)
                    ->where('receiver_id', $authId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return $this->successResponse($messages, 'Chat fetched successfully');
    }

    public function send(Request $request, $id)
    {
        $chat = ChatSetting::first();

        if (! $chat) {
            return $this->errorResponse('Chat setting not found', 404);
        }

        $request->validate([
            'message' => 'required|string|max:'.$chat->message_length,
        ]);

        $receiver = User::find($id);

        if (! $receiver) {
            return $this->errorResponse('Receiver not found', 404);
        }

        if ($id == Auth::id()) {
            return $this->errorResponse('You cannot send message to yourself', 403);
        }

        $msg = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $id,
            'message' => $request->message,
        ]);

        $msg->load(['sender:id,fname', 'receiver:id,fname']);

        broadcast(new ChatMessageSent($msg))->toOthers();

        return $this->successResponse($msg, 'Message sent successfully');
    }

    public function message()
    {
        $msg = 'hello';
        broadcast(new ChatMessageSent($msg))->toOthers();

        return $this->successResponse($msg, 'Message sent successfully');
    }
}
