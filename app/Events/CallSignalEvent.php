<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CallSignalEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

<<<<<<< HEAD
    public $callId;
    public $form;
    public $to;
    public $data;
    public $type;

    
    public function __construct($callId, $form, $to, $type, $data = [])
    {
        $this->callId = $callId;
        $this->form = $form;
        $this->to = $to;
        $this->data = $data;
        $this->type = $type;
=======
    // public $callId;
    // public $form;
    // public $to;
    // public $data;
    // public $type;


    public function __construct(
        public int $from,
        public int $to,
        public string $type,
        public array $payload
    ) {
>>>>>>> 001eb97c162699e903a0b0e3daad5245a9ebe9cf
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
<<<<<<< HEAD
=======
        // Log::info('data = ' . $this->data);
>>>>>>> 001eb97c162699e903a0b0e3daad5245a9ebe9cf
        return new PrivateChannel('video-call.' . $this->to);
    }

    public function broadcastAs()
    {
        return 'call.signal';
    }
}