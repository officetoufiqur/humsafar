<?php

namespace App\Http\Controllers\Api;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    use ApiResponse;

    public function messages($id)
    {
        $authId = Auth::id();

        $messages = Message::with(['sender:id,fname', 'receiver:id,fname'])
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
        $request->validate([
            'message' => 'required|string|max:2000',
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
}
