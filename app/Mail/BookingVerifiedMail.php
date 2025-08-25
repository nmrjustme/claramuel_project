<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $booking;
    public $qrCodeUrl;
    public $customMessage;
    
    public function __construct($booking, $qrCodeUrl, $customMessage)
    {
        $this->booking = $booking;
        $this->qrCodeUrl = $qrCodeUrl;
        $this->customMessage = $customMessage;
    }
    
    public function build()
    {
        $mail = $this->subject('Payment Verified - Reservation Code '.$this->booking->code)
            ->view('emails.mail_receipt')
            ->with([
                'booking' => $this->booking,
                'qrCodeUrl' => $this->qrCodeUrl, // Make sure this is passed to the view
                'customMessage' => $this->customMessage
            ]);
        return $mail;
    }
}
