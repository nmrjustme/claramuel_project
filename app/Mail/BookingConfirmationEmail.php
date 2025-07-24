<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\FacilityBookingLog;

class BookingConfirmationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $verificationUrl;
    public $customMessage;

    public function __construct(FacilityBookingLog $booking, $verificationUrl, $customMessage = null)
    {
        $this->booking = $booking;
        $this->verificationUrl = $verificationUrl;
        $this->customMessage = $customMessage;
    }

    public function build()
    {
        return $this->markdown('emails.booking_confirmation')
                   ->subject('Booking Confirmation');
    }
}