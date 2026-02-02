<?php

namespace App\Http\Controllers;

use App\Events\CallSignalEvent;
use App\Events\WebRtcSignalEvent;
use App\Models\Call;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// app/Http/Controllers/CallController.php
class CallController extends Controller
{
    public function start(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'type' => 'required|in:audio,video',
        ]);

        $call = Call::create([
            'caller_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'type' => $request->type,
        ]);

        broadcast(new WebRtcSignalEvent(
            auth()->id(),
            $request->receiver_id,
            'incoming_call',
            ['call' => $call]
        ))->toOthers();

        return response()->json($call);
    }

    public function signal(Request $request)
    {
        $request->validate([
            'to' => 'required|exists:users,id',
            'type' => 'required|string',
            'payload' => 'required|array',
        ]);

        broadcast(new WebRtcSignalEvent(
            auth()->id(),
            $request->to,
            $request->type,
            $request->payload
        ))->toOthers();

        return response()->json(['ok' => true]);
    }
}
