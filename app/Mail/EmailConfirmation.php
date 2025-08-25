<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $bookingData;

    public function __construct($otp, $bookingData)
    {
        $this->otp = $otp;
        $this->bookingData = $bookingData;
    }

    public function build()
    {
        return $this->subject('Your Booking OTP Code')
                    ->view('emails.email_confirmation')
                    ->with([
                        'otp' => $this->otp,
                        'booking' => $this->bookingData
                    ]);
    }
}