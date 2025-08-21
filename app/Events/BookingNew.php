<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\FacilityBookingLog;

class BookingNew implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $booking;
    
    public function __construct(FacilityBookingLog $booking)
    {
        $this->booking = [
            'id' => $booking->id,
            'status' => $booking->status ?? 'pending_confirmation',
            'is_read' => $booking->is_read,
            'user' => [
                'firstname' => $booking->user->firstname,
                'lastname' => $booking->user->lastname,
                'email' => $booking->user->email,
                'phone' => $booking->user->phone,
            ],
            'created_at' => $booking->created_at->toDateTimeString()
        ];
    }
    
    public function broadcastOn(): Channel
    {
        return new Channel('bookings');
    }

    public function broadcastAs(): string
    {
        return 'booking.created';
    }
}
