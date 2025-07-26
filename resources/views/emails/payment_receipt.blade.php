<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Verified - {{ $payment->bookingLog->reference }}</title>
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
    </style>
    
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Verified</h1>
            <p>Booking Reference: {{ $payment->bookingLog->reference }}</p>
        </div>
        
        <div class="content">
            
            <div class="receipt">

            <div class="section">
                <h2 class="section-title">Guest Information</h2>
                
                <div class="detail-row">
                    <span class="detail-label">Full Name:</span>
                    <span class="detail-value">{{ $payment->bookingLog->user->firstname }} {{ $payment->bookingLog->user->lastname }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $payment->bookingLog->user->email }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value">{{ $payment->bookingLog->user->phone }}</span>
                </div>
                
                <h2 style="margin-top: 0;">Payment Details</h2>
                
                <div class="detail-row">
                    <span class="detail-label">Advance Amount:</span>
                    <span class="detail-value">₱{{ number_format($payment->amount, 2) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount Paid:</span>
                    <span class="detail-value">₱{{ number_format($payment->amount_paid, 2) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Balance (To be paid upon checkin):</span>
                    <span class="detail-value">₱{{ number_format(($payment->bookingLog->details->first()->total_price - $payment->amount_paid), 2) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">{{ $payment->verified_at->format('F j, Y \a\t g:i A') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Method:</span>
                    <span class="detail-value">GCash</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Reference:</span>
                    <span class="detail-value">{{ $payment->reference_no }}</span>
                </div>
                
                <!-- Booking Details Section -->
                <h2 style="margin-top: 20px;">Booking Summary</h2>
                
                @php 
                    $totalAmount = 0;
                @endphp
                
                @foreach($payment->bookingLog->summaries as $summary)
                
                    @php
                        $subtotal = $summary->bookingDetails->sum('total_price');
                        $totalAmount += $subtotal;
                        
                    @endphp
                    <div style="margin-bottom: 20px; padding: 15px; background: #f0f0f0; border-radius: 5px;">
                    <div class="detail-row">
                        <span class="detail-label">Room Type:</span>
                        <span class="detail-value">{{ $summary->facility->name }} {{ $summary->facility->price }}/Per night</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Check-in:</span>
                        <span class="detail-value">
                            {{ $summary->bookingDetails->first()->checkin_date->format('F j, Y') }}
                        </span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Check-out:</span>
                        <span class="detail-value">
                            {{ $summary->bookingDetails->first()->checkout_date->format('F j, Y') }}
                        </span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Duration:</span>
                        <span class="detail-value">
                            {{ $summary->bookingDetails->first()->checkin_date->diffInDays($summary->bookingDetails->first()->checkout_date) }} night(s)
                        </span>
                    </div>
                    
                    <span class="detail-value">total of {{ ($summary->facility->price * $summary->bookingDetails->first()->checkin_date->diffInDays($summary->bookingDetails->first()->checkout_date)) }}</span>
                    @if($summary->breakfast)
                    <div class="detail-row">
                        <span class="detail-label">Breakfast:</span>
                        <span class="detail-value">Included/₱{{ number_format($summary->breakfast->price) }}</span>
                    </div>
                    @endif
                </div>
                @endforeach
                
                <!-- Display the total amount from the payment -->
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
        
        <div class="footer">
            <p>Thank you for choosing our services!</p>
            <p>We will wait for your arrival.</p>
            <p>If you have any questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html>