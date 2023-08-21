<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BusLocationEvent implements ShouldBroadCast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $coordinates;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, $coordinates)
    {
        $this->user_id = $user_id;
        $this->coordinates = $coordinates;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('bus-location');
    }

    public function broadcastAs()
    {
        return 'new-bus-location';
    }
}
