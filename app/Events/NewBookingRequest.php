<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewBookingRequest implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id, $guest, $status;

    /**
     * Create a new event instance.
     */
    public function __construct($booking)
    {
        $this->id = $booking->id;
        $this->guest = $booking->user->firstname . ' ' . $booking->user->lastname;
        $this->status = $booking->status;
    }
    
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel('booking-channel'); 
    }
    
    public function broadcastAs()
    {
        return 'new-booking';
    }
}
