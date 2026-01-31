<?php

namespace App\Http\Controllers;

use App\Events\CallSignalEvent;
use App\Models\Call;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CallController extends Controller
{
    use ApiResponse;

     public function start(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'type' => 'required|in:audio,video',
        ]);

        if ($request->receiver_id == Auth::id()) {
            return response()->json(['message' => 'Cannot call yourself'], 403);
        }

        $call = Call::create([
            'caller_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'type' => $request->type,
        ]);

        broadcast(new CallSignalEvent(
            $call->id,
            Auth::id(),
            'incoming_call',
            $call
        ))->toOthers();

        return $this->successResponse($call, 'Call started');
    }

    public function accept(Call $call)
    {
        $this->authorizeCall($call);

        $call->update([
            'status' => 'accepted',
            'started_at' => now(),
        ]);

        broadcast(new CallSignalEvent(
            $call->id,
            Auth::id(),
            'accepted',
            $call
        ))->toOthers();

        return response()->json(['message' => 'Call accepted']);
    }

    public function reject(Call $call)
    {
        $this->authorizeCall($call);

        $call->update([
            'status' => 'rejected',
            'ended_at' => now(),
        ]);

        broadcast(new CallSignalEvent(
            $call->id,
            Auth::id(),
            'rejected',
            $call
        ))->toOthers();

        return response()->json(['message' => 'Call rejected']);
    }

    public function signal(Request $request, Call $call)
    {
        $this->authorizeCall($call);

        broadcast(new CallSignalEvent(
            $call->id,
            Auth::id(),
            $request->type, 
            $request->data
        ))->toOthers();

        return response()->json(['status' => 'sent']);
    }

    public function end(Call $call)
    {
        $this->authorizeCall($call);

        $call->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        broadcast(new CallSignalEvent(
            $call->id,
            Auth::id(),
            'ended',
            $call
        ))->toOthers();

        return response()->json(['message' => 'Call ended']);
    }

    private function authorizeCall(Call $call)
    {
        if (!in_array(Auth::id(), [$call->caller_id, $call->receiver_id])) {
            abort(403);
        }
    }
}
