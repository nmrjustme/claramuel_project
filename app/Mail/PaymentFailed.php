<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentFailed extends Mailable
{
    use Queueable, SerializesModels;

    public $firstname;
    /**
     * Create a new message instance.
     */
    public function __construct($firstname)
    {
        $this->firstname = $firstname;
    }

    public function build() 
    {
        $mail = $this->subject('Booking Failed')
            ->view('emails.booking_failed')
            ->with([
                'firstname' => $this->firstname,
            ]);
        return $mail;
    }
}
