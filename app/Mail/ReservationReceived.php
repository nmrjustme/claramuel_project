<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $pdf;

    /**
     * Create a new message instance.
     */
    public function __construct($booking, $pdf)
    {
        $this->booking = $booking;
        $this->pdf = $pdf;
    }

    public function build()
    {
        $mail = $this->subject('Booking Received - Reservation Code: ' . $this->booking->code)
            ->view('emails.booking_received')
            ->with([
                'booking' => $this->booking,
            ]);

        // Attach PDF invoice
        if ($this->pdf) {
            $mail->attachData($this->pdf->output(), 'invoice_' . $this->booking->code . '.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}