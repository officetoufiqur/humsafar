<?php

namespace App\Http\Controllers;

use App\Events\WebRtcSignalEvent;
use App\Models\Call;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CallController extends Controller
{
    public function start(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'type' => 'required|in:audio,video',
        ]);

        $call = Call::create([
            'caller_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'type' => $request->type,
        ]);

        $caller = Auth::user();

        broadcast(new WebRtcSignalEvent(
            Auth::id(),
            $request->receiver_id,
            'incoming_call',
            ['call' => $call],
            $caller,
        ))->toOthers();

        return response()->json($call);
    }

    public function signal(Request $request)
    {
        $request->validate([
            'to' => 'required|exists:users,id',
            'from' => 'required|exists:users,id',
            'type' => 'required|string',
            'payload' => 'nullable|array',
        ]);

        Log::info($request->all());

        $call = Call::where('caller_id', $request->from)
            ->where('receiver_id', $request->to)
            ->first();

        if ($call) {
            if ($request->type === 'webrtc_answer' && ! $call->started_at) {
                $call->started_at = now();
            }

            if ($request->type === 'call_end') {
                $call->ended_at = now();
            }

            $call->save();
        }

        broadcast(new WebRtcSignalEvent(
            Auth::id(),
            $request->to,
            $request->type,
            $request->payload,
            Auth::user()
        ))->toOthers();

        return response()->json(['ok' => true]);
    }
}
