<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\FacilityBookingLog;

class BookingCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;

    /**
     * Create a new event instance.
     *
     * @param FacilityBookingLog $booking
     */
    public function __construct(FacilityBookingLog $booking)
    {
        $this->booking = $booking->load('user'); // Eager load user relationship
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn()
    {
        return new Channel('booking-channel'); // Public channel
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'BookingCreated'; // Matches the frontend listener
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'booking' => [
                'id' => $this->booking->id,
                'user' => [
                    'firstname' => $this->booking->user->firstname,
                    'lastname' => $this->booking->user->lastname,
                ],
                'created_at' => $this->booking->created_at->toDateTimeString(),
                'status' => $this->booking->status
            ]
        ];
    }
}