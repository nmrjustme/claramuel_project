<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-in Receipt - {{ $payment->bookingLog->reference }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
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
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
            align-items: center;
        }
        .detail-label {
            font-weight: bold;
            width: 120px;
        }
        .detail-value {
            flex: 1;
        }
        .room-details {
            margin-bottom: 20px;
            padding: 15px;
            background: #f0f0f0;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid #eeeeee;
            font-size: 14px;
            color: #777777;
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
        .status-select {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
            margin-left: 10px;
        }
        .status-pending {
            color: #ff9800;
        }
        .status-paid {
            color: #4caf50;
        }
        .save-btn {
            padding: 5px 10px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }
        .save-btn:hover {
            background: #0b7dda;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .status-select, .save-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="no-print" style="text-align: center; margin-top: 20px;">
                <button onclick="window.print()" style="padding: 10px 20px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Print Receipt
                </button>
            </div>
            <h1>Check-in Receipt</h1>
            <p>Booking Reference: {{ $payment->bookingLog->reference }}</p>
            <p>Check-in Date: {{ now()->format('F j, Y \a\t g:i A') }}</p>
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
                        <span class="detail-value">{{ $payment->bookingLog->user->phone ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="section">
                    <h2 class="section-title">Booking Summary</h2>
                    
                    @php 
                        $totalAmount = 0;
                    @endphp
                    
                    @foreach($payment->bookingLog->summaries as $summary)
                        @php
                            $facility = $summary->facility;
                            $bookingDetail = $summary->bookingDetails->first();
                            
                            if ($bookingDetail) {
                                $nights = $bookingDetail->checkin_date->diffInDays($bookingDetail->checkout_date);
                                $subtotal = $facility->price * $nights;
                                $totalAmount += $subtotal;
                            }
                        @endphp
                        
                        @if($bookingDetail)
                            <div class="room-details">
                                <div class="detail-row">
                                    <span class="detail-label">Room Type:</span>
                                    <span class="detail-value">{{ $facility->name }} (₱{{ number_format($facility->price, 2) }}/night)</span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Room Number:</span>
                                    <span class="detail-value">{{ $facility->room_number ?? 'Not assigned' }}</span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Check-in:</span>
                                    <span class="detail-value">
                                        {{ $bookingDetail->checkin_date->format('F j, Y g:i A') }}
                                    </span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Check-out:</span>
                                    <span class="detail-value">
                                        {{ $bookingDetail->checkout_date->format('F j, Y g:i A') }}
                                    </span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Duration:</span>
                                    <span class="detail-value">
                                        {{ $nights }} night(s)
                                    </span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Subtotal:</span>
                                    <span class="detail-value">₱{{ number_format($subtotal, 2) }}</span>
                                </div>
                                
                                @if($summary->breakfast)
                                <div class="detail-row">
                                    <span class="detail-label">Breakfast:</span>
                                    <span class="detail-value">Included</span>
                                </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                    
                    <div class="detail-row" style="margin-top: 15px; font-weight: bold;">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value">₱{{ number_format($totalAmount, 2) }}</span>
                    </div>
                </div>

                <div class="section">
                    <h2 class="section-title">Payment Information</h2>
                    
                    <div class="detail-row">
                        <span class="detail-label">Amount Paid:</span>
                        <span class="detail-value">₱{{ number_format($payment->amount, 2) }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Remaining Amount:</span>
                        <span class="detail-value">₱{{ number_format(($payment->amount), 2) }}</span>
                        <span class="detail-value">
                            Status: 
                            <select id="status-select" class="status-select no-print">
                                <option value="pending" {{ $payment->remaining_balance_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $payment->remaining_balance_status === 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                            <button id="save-btn" class="save-btn no-print">Save</button>
                        </span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Method:</span>
                        <span class="detail-value">{{ ucfirst($payment->method) }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Payment Date:</span>
                        <span class="detail-value">{{ $payment->verified_at->format('F j, Y \a\t g:i A') }}</span>
                    </div>
                    
                    @if($payment->reference_no)
                    <div class="detail-row">
                        <span class="detail-label">Reference No:</span>
                        <span class="detail-value">{{ $payment->reference_no }}</span>
                    </div>
                    @endif
                    
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">Verified</span>
                    </div>
                </div>
                
                @if(isset($qrCodeUrl))
                <div class="qr-code">
                    <img src="{{ $qrCodeUrl }}" alt="Verification QR Code">
                    <p>Present this QR code when claiming your reservation</p>
                </div>
                @endif
            </div>
        </div>
        
        <div class="footer">
            <p>Thank you for choosing our services!</p>
            <p>We look forward to serving you.</p>
            <p>For any assistance, please contact our front desk.</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#save-btn').click(function() {
                const newStatus = $('#status-select').val();
                const paymentId = {{ $payment->id }};
                
                $.ajax({
                    url: '/payments/' + paymentId + '/update-remaining-status',
                    method: 'PUT',
                    data: {
                        remaining_status: newStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update display
                            $('#status-display').text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                            $('#status-display').removeClass('status-pending status-paid');
                            $('#status-display').addClass(newStatus === 'paid' ? 'status-paid' : 'status-pending');
                            
                            alert('Status updated successfully!');
                        } else {
                            alert('Error updating status: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Error updating status. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>
</html>