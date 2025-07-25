<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\FacilityBookingLog;

class NewBookingRequest implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;

    public function __construct(FacilityBookingLog $booking)
    {
        $this->booking = $booking->load('user');
    }

    public function broadcastOn()
    {
        return new Channel('booking-channel');
    }

    public function broadcastAs()
    {
        return 'NewBookingRequest';
    }
}