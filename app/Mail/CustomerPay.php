<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Payments;

class CustomerPay extends Mailable
{
    use Queueable, SerializesModels;

        public $payment;
    
    /**
     * Create a new message instance.
     */
    public function __construct(Payments $payment)
    {
        $this->payment = $payment;
    }
    
    public function build()
    {
        return $this->markdown('emails.customer_pay')
            ->subject('New Payment Submitted');
    }
}
