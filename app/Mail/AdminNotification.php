<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\FacilityBookingLog;

class AdminNotification extends Mailable
{
    use Queueable, SerializesModels;
    
    public $booking;
    
    /**
     * Create a new message instance.
     */
    public function __construct(FacilityBookingLog $booking)
    {
        $this->booking = $booking;
    }
    
    public function build()
    {
        return $this->markdown('emails.admin_notification')
            ->subject('New Booking');
    }
}
