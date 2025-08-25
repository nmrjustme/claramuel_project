<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Reservation Request is Confirmed - {{ $booking->code }}</title>
    <style>
                body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #eeeeee;
        }
        .content {
            padding: 20px 0;
        }
        .receipt {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .qr-code {
            text-align: center;
            margin: 25px 0;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        .qr-code img {
            width: 200px;
            height: 200px;
            display: block;
            margin: 0 auto 15px;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid #eeeeee;
            font-size: 14px;
            color: #777777;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }
        .detail-label {
            font-weight: bold;
            width: 120px;
        }
        .detail-value {
            flex: 1;
        }
        .resort-info {
            margin-top: 15px;
            font-style: italic;
        }
        .contact-info {
            margin-top: 10px;
        }

        .custom-message {
            background: #e6f4ea; /* soft green for verified/success */
            border: 1px solid #b5dfc0;
            border-left: 6px solid #34a853; /* Google green style accent */
            padding: 15px 20px;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 15px;
            color: #2d572c;
        }

        .custom-message p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your reservation is confirmed.</h1>
            <p>Reservation Code: {{ $booking->code }}</p>
        </div>
        
        @if($customMessage)
            <div class="custom-message">
                <p>{{ $customMessage }}</p>
            </div>
        @endif
        
        <div class="content">
            <div class="receipt">
                <div class="section">
                    @php 
                        $totalAmount = 0;
                        foreach ($booking->summaries as $summary) {
                            $subtotal = $summary->bookingDetails->sum('total_price');
                            $totalAmount += $subtotal;
                        }
                        
                        // Get the latest payment or first payment
                        $payment = $booking->payments->first();
                        $totalPaid = $booking->payments->sum('amount_paid');
                    @endphp
                    
                    <h2 class="section-title">Guest Information</h2>
                    
                    <div class="detail-row">
                        <span class="detail-label">Full Name:</span>
                        <span class="detail-value">{{ $booking->user->firstname }} {{ $booking->user->lastname }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value">{{ $booking->user->email }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Phone:</span>
                        <span class="detail-value">{{ $booking->user->phone }}</span>
                    </div>
                    
                    <h2 style="margin-top: 0;">Payment Details</h2>
                    
                    @if($payment)
                    <div class="detail-row">
                        <span class="detail-label">Advance Amount:</span>
                        <span class="detail-value">₱{{ number_format($payment->amount, 2) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Amount Paid:</span>
                        <span class="detail-value">₱{{ number_format($totalPaid, 2) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Balance (To be paid upon checkin):</span>
                        <span class="detail-value">₱{{ number_format(($totalAmount - $totalPaid), 2) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">
                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('F j, Y \a\t g:i A') }}
                        </span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Method:</span>
                        <span class="detail-value">GCash</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Reference:</span>
                        <span class="detail-value">{{ $payment->reference_no }}</span>
                    </div>
                    @else
                    <div class="detail-row">
                        <span class="detail-label">Payment Status:</span>
                        <span class="detail-value">No payment information available</span>
                    </div>
                    @endif
                    
                    <!-- Booking Details Section -->
                    <h2 style="margin-top: 20px;">Booking Summary</h2>
                    
                    @php 
                        $totalAmount = 0;
                    @endphp
                    
                    @foreach($booking->summaries as $summary)
                        @php
                            $subtotal = $summary->bookingDetails->sum('total_price');
                            $totalAmount += $subtotal; 
                            
                            $guestsForFacility = $booking->guestDetails
                                ->where('facility_id', $summary->facility_id)
                                ->groupBy('guest_type_id')
                                ->map(function($items) {
                                    return [
                                        'type' => $items->first()->guestType->type ?? 'Unknown',
                                        'quantity' => $items->sum('quantity')
                                    ];
                            });
                        @endphp
                        <div style="margin-bottom: 20px; padding: 15px; background: #f0f0f0; border-radius: 5px;">
                            <div class="detail-row">
                                <span class="detail-label">Room Type:</span>
                                <span class="detail-value">{{ $summary->facility->name }} ₱{{ number_format($summary->facility->price, 2) }}/Per night</span>
                            </div>
                            
                            @if($guestsForFacility->count() > 0)
                                <table style="width: 100%; border-collapse: collapse;">
                                    @foreach($guestsForFacility as $guest)
                                    <tr>
                                        <td style="padding: 5px; border-bottom: 1px solid #ddd; width: 70%;">{{ $guest['type'] }}</td>
                                        <td style="padding: 5px; border-bottom: 1px solid #ddd; text-align: right;">{{ $guest['quantity'] }} guest(s)</td>
                                    </tr>
                                    @endforeach
                                    <tr style="font-weight: bold;">
                                        <td style="padding: 5px;">Total Guests</td>
                                        <td style="padding: 5px; text-align: right;">{{ $guestsForFacility->sum('quantity') }}</td>
                                    </tr>
                                </table>
                            @else
                                <p style="color: #666; font-style: italic;">No guest details recorded</p>
                            @endif
                            
                            <div class="detail-row">
                                <span class="detail-label">Check-in:</span>
                                <span class="detail-value">
                                    {{ \Carbon\Carbon::parse($summary->bookingDetails->first()->checkin_date)->format('F j, Y') }}
                                </span>
                            </div>
                            
                            <div class="detail-row">
                                <span class="detail-label">Check-out:</span>
                                <span class="detail-value">
                                    {{ \Carbon\Carbon::parse($summary->bookingDetails->first()->checkout_date)->format('F j, Y') }}
                                </span>
                            </div>
                            
                            <div class="detail-row">
                                <span class="detail-label">Duration:</span>
                                <span class="detail-value">
                                    {{ \Carbon\Carbon::parse($summary->bookingDetails->first()->checkin_date)->diffInDays($summary->bookingDetails->first()->checkout_date) }} night(s)
                                </span>
                            </div>
                            
                            <div class="detail-row">
                                <span class="detail-label">Subtotal:</span>
                                <span class="detail-value">₱{{ number_format($summary->facility->price * \Carbon\Carbon::parse($summary->bookingDetails->first()->checkin_date)->diffInDays($summary->bookingDetails->first()->checkout_date), 2) }}</span>
                            </div>
                            
                            @if($summary->breakfast)
                            <div class="detail-row">
                                <span class="detail-label">Breakfast:</span>
                                <span class="detail-value">Included/₱{{ number_format($summary->breakfast->price, 2) }}</span>
                            </div>
                            @endif
                        </div>
                    @endforeach
                    
                    <!-- Display the total amount -->
                    <div class="detail-row" style="margin-top: 15px; font-weight: bold;">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value">₱{{ number_format($totalAmount, 2) }}</span>
                    </div>
                    
                    <div class="qr-code">
                        <img src="{{ $qrCodeUrl }}" alt="Verification QR Code">
                        <p>Present this QR code when claiming your reservation</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>Thank you for choosing our services!</p>
            <p>We will wait for your arrival.</p>
            
            <div class="resort-info">
                <p>Narra Street, Brgy. Marana 3rd, Ilagan, 3300 Isabela, Philippines</p>
            </div>
            
            <div class="contact-info">
                <p>Contact: +63 995 290 1333</p>
                <p>Email: mtclaramuelresort@gmail.com</p>
            </div>
            
            <p>If you have any questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html>