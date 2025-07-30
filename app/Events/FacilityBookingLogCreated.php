<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class FacilityBookingLogCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $booking;

    public function __construct($booking)
    {
        Log::debug('FacilityBookingLogCreated event constructed', ['booking_id' => $booking->id]);
        $this->booking = $booking;
    }
    
    public function broadcastOn()
    {
        Log::debug('FacilityBookingLogCreated broadcastOn called', ['channel' => 'booking-log-channel']);
        return new Channel('booking-log-channel');
    }
    
    public function broadcastAs()
    {
        Log::debug('FacilityBookingLogCreated broadcastAs called', ['event_name' => 'new-booking-log']);
        return 'new-booking-log';
    }

    public function broadcastWith()
    {
        $payload = [
            'id' => $this->booking->id,
            'firstname' => $this->booking->user->firstname,
            'lastname' => $this->booking->user->lastname,
            'status' => $this->booking->status
        ];
        
        Log::debug('FacilityBookingLogCreated broadcastWith called', ['payload' => $payload]);
        return $payload;
    }
}