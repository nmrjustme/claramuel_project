<?php

namespace App\Mail;

use App\Models\FacilityBookingLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingRejectionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $customMessage;

    /**
     * Create a new message instance.
     *
     * @param FacilityBookingLog $booking
     * @param string|null $customMessage
     */
    public function __construct(FacilityBookingLog $booking, ?string $customMessage = null)
    {
        $this->booking = $booking;
        $this->customMessage = $customMessage;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Booking Has Been Rejected')
                    ->markdown('emails.booking_rejection')
                    ->with([
                        'booking' => $this->booking,
                        'customMessage' => $this->customMessage
                    ]);
    }
}