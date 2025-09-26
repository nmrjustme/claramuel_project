<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your reservation request has been received in mtclaramuel</title>
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
            background: #fff9e6; /* soft yellow for verified/success */
            border: 1px solid #f0e6b5;
            border-left: 6px solid #f4c542; /* yellow accent */
            padding: 25px;
            border-radius: 8px;
            margin: 20px 0;
            color: #7a5c00; /* dark yellow-brown text */
            position: relative;
            overflow: hidden;
        }

        .custom-message p {
            margin: 0 0 15px 0;
            line-height: 1.6;
        }
        
        .custom-message:before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23b5dfc0' width='100px' height='100px'%3E%3Cpath d='M0 0h24v24H0z' fill='none'/%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z'/%3E%3C/svg%3E") no-repeat;
            background-size: contain;
            opacity: 0.2;
            transform: translate(30px, -30px);
        }
        
        .confirmation-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #2e7d32;
            display: flex;
            align-items: center;
        }
        
        .confirmation-title:before {
            content: "✓";
            display: inline-block;
            width: 28px;
            height: 28px;
            background: #34a853;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 28px;
            margin-right: 10px;
            font-size: 16px;
        }
        
        .footer {
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid #eeeeee;
            font-size: 14px;
            color: #777777;
        }
        
        /* Addons styling */
        .addons-section {
            margin: 20px 0;
            padding: 15px;
            background: #f0f8ff;
            border-radius: 8px;
            border-left: 4px solid #4a90e2;
        }
        
        .addon-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #d0e4ff;
        }
        
        .addon-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .addon-name {
            font-weight: bold;
        }
        
        .addon-details {
            color: #666;
            font-size: 0.9em;
        }
        
        .addon-price {
            text-align: right;
            min-width: 100px;
        }
        
        .addons-total {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #c0d8f0;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }
        
        .no-addons {
            color: #666;
            font-style: italic;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="custom-message">
            <p>Dear {{ $booking->user->firstname }},</p>
            
            <p>Your reservation request has been <strong>successfully received</strong>. We’re pleased to inform you that a confirmation will be sent shortly to your <strong>registered email address</strong> and <strong>mobile number</strong>.</p>            
            <div class="resort-info">
                <p><strong>Resort Location:</strong><br>
                Narra Street, Brgy. Marana 3rd, Ilagan, 3300 Isabela, Philippines</p>
            </div>
            
            <div class="contact-info">
                <p><strong>Contact Information:</strong><br>
                Phone: +63 995 290 1333<br>
                Email: mtclaramuelresort@gmail.com</p>
            </div>
            
            <p>If you have any questions, special requests, or need further assistance, please don’t hesitate to contact us. We want your stay with us to be both <strong>memorable</strong> and <strong>comfortable</strong>.</p>
            
            <p>Thank you for choosing <strong>Mt. ClaRamuel Resort</strong></p>
        </div>

        <h1>Your Booking Details</h1>
        <div class="content">
            <div class="receipt">
                <div class="section">
                    @php 
                        // Calculate total amount from booking summaries
                        $totalAmount = 0;
                        foreach ($booking->summaries as $summary) {
                            $subtotal = $summary->bookingDetails->sum('total_price');
                            $totalAmount += $subtotal;
                        }
                        
                        // Calculate addons total if they exist
                        $addonsTotal = 0;
                        if ($booking->guestAddons && $booking->guestAddons->count() > 0) {
                            $addonsTotal = $booking->guestAddons->sum('total_cost');
                            $totalAmount += $addonsTotal;
                        }
                        
                        // Calculate payment information correctly
                        $advancePaid = $booking->payments->sum('amount');
                        $totalPaid = $advancePaid;
                        $balance = $totalAmount - $totalPaid;
                        
                        // Get the first payment for reference details
                        $payment = $booking->payments->first();
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
                    
                    <h2 style="margin-top: 20px;">Payment Details</h2>
                    
                    @if($booking->payments && $booking->payments->count() > 0)
                    <div class="detail-row" style="font-weight: bold;">
                        <span class="detail-label">Advance Paid:</span>
                        <span class="detail-value">₱{{ number_format($totalPaid, 2) }}</span>
                    </div>
                    <div class="detail-row" style="font-weight: bold; color: {{ $balance > 0 ? 'red' : 'green' }};">
                        <span class="detail-label">Balance:</span>
                        <span class="detail-value">
                            ₱{{ number_format(abs($balance), 2) }}
                            {{ $balance > 0 ? '(Due)' : '(FULLY PAID)' }}
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">
                            {{ $payment && $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('F j, Y \a\t g:i A') : 'N/A' }}
                        </span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Method:</span>
                        <span class="detail-value">{{ $payment && $payment->method ? ucfirst($payment->method) : 'GCash' }}</span>
                    </div>
                    
                    @if($payment && $payment->reference_no)
                    <div class="detail-row">
                        <span class="detail-label">Reference:</span>
                        <span class="detail-value">{{ $payment->reference_no }}</span>
                    </div>
                    @endif
                    @else
                    <div class="detail-row">
                        <span class="detail-label">Payment Status:</span>
                        <span class="detail-value">No payment information available</span>
                    </div>
                    @endif
                    
                    <!-- Addons Section -->
                    @if($booking->guestAddons && $booking->guestAddons->count() > 0)
                    <div class="addons-section">
                        <h2 style="margin-top: 0;">Additional Services</h2>
                        
                        @foreach($booking->guestAddons as $addon)
                        <div class="addon-item">
                            <div>
                                <div class="addon-name">{{ $addon->type }}</div>
                                <div class="addon-details">Quantity: {{ $addon->quantity }} × ₱{{ number_format($addon->cost, 2) }}</div>
                            </div>
                            <div class="addon-price">₱{{ number_format($addon->total_cost, 2) }}</div>
                        </div>
                        @endforeach
                        
                        <div class="addons-total">
                            <span>Addons Total:</span>
                            <span>₱{{ number_format($addonsTotal, 2) }}</span>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Booking Details Section -->
                    <h2 style="margin-top: 20px;">Booking Summary</h2>
                    
                    @php 
                        $facilitiesTotal = 0;
                    @endphp
                    
                    @foreach($booking->summaries as $summary)
                        @php
                            $subtotal = $summary->bookingDetails->sum('total_price');
                            $facilitiesTotal += $subtotal; 
                            
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
                                <span class="detail-value">{{ $summary->facility->name }} ₱{{ number_format($summary->facility_price, 2) }}/Per night</span>
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
                                <span class="detail-value">₱{{ number_format($summary->facility_price * \Carbon\Carbon::parse($summary->bookingDetails->first()->checkin_date)->diffInDays($summary->bookingDetails->first()->checkout_date), 2) }}</span>
                            </div>
                            
                            @if($summary->breakfast)
                            <div class="detail-row">
                                <span class="detail-label">Breakfast:</span>
                                <span class="detail-value">Included/₱{{ number_format($summary->breakfast_price, 2) }}</span>
                            </div>
                            @endif
                        </div>
                    @endforeach
                    
                    <!-- Display the total amount -->
                    <div class="detail-row" style="margin-top: 15px; font-weight: bold;">
                        <span class="detail-label">Facilities Total:</span>
                        <span class="detail-value">₱{{ number_format($facilitiesTotal, 2) }}</span>
                    </div>
                    
                    @if($booking->guestAddons && $booking->guestAddons->count() > 0)
                    <div class="detail-row" style="font-weight: bold;">
                        <span class="detail-label">Addons Total:</span>
                        <span class="detail-value">₱{{ number_format($addonsTotal, 2) }}</span>
                    </div>
                    @endif
                    
                    <div class="detail-row" style="margin-top: 15px; font-weight: bold; border-top: 2px solid #333; padding-top: 10px;">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value">₱{{ number_format($totalAmount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>Thank you for choosing our services!</p>
        </div>
    </div>
</body>
</html>