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
    public $transport_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, $coordinates, $transport_id)
    {
        $this->user_id = $user_id;
        $this->coordinates = $coordinates;
        $this->transport_id = $transport_id;
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
