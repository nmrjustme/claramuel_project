<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PaymentVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $qrCodeUrl;
    public $customMessage;

    public function __construct($payment, $qrCodeUrl, $customMessage)
    {
        $this->payment = $payment;
        $this->qrCodeUrl = $qrCodeUrl;
        $this->customMessage = $customMessage;
    }

    public function build()
    {
        $mail = $this->subject('Payment Verified - Receipt '.$this->payment->bookingLog->reference)
            ->view('emails.payment_receipt')
            ->with([
                'payment' => $this->payment,
                'qrCodeUrl' => $this->qrCodeUrl, // Make sure this is passed to the view
                'customMessage' => $this->customMessage
            ]);
        return $mail;
    }
}