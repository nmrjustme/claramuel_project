<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $invoiceNumber }}</title>
    <style>
        /* Base Styles */
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        
        /* Header Styles */
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .invoice-header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
            font-weight: bold;
        }
        
        .invoice-info {
            margin-top: 10px;
        }
        
        .invoice-info strong {
            display: inline-block;
            min-width: 100px;
        }
        
        /* Section Styles */
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #555;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        
        /* Resort & Guest Info */
        .resort-info, .guest-info {
            display: inline-block;
            vertical-align: top;
            width: 48%;
        }
        
        .guest-info {
            float: right;
        }
        
        /* Table Styles */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .details-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: left;
            padding: 12px 8px;
            border: 1px solid #ddd;
        }
        
        .details-table td {
            padding: 10px 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        .details-table .text-right {
            text-align: right;
        }
        
        .details-table .text-center {
            text-align: center;
        }
        
        /* Totals Section */
        .totals-section {
            margin-top: 20px;
            text-align: right;
            width: 300px;
            float: right;
        }
        
        .total-row {
            margin: 8px 0;
            display: flex;
            justify-content: space-between;
        }
        
        .total-label {
            font-weight: bold;
            min-width: 150px;
            text-align: right;
            padding-right: 10px;
        }
        
        .total-value {
            min-width: 120px;
            text-align: right;
        }
        
        .grand-total {
            font-size: 14px;
            font-weight: bold;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        /* Payment Information */
        .payment-info {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
            clear: both;
        }
        
        .payment-details {
            margin-top: 10px;
        }
        
        .payment-row {
            display: flex;
            margin-bottom: 5px;
        }
        
        .payment-label {
            font-weight: bold;
            min-width: 120px;
        }
        
        /* Thank You Message */
        .thank-you {
            text-align: center;
            margin-top: 40px;
            font-style: italic;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        
        /* Utility Classes */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        
        .text-bold {
            font-weight: bold;
        }
        
        .mb-10 {
            margin-bottom: 10px;
        }
        
        .mb-20 {
            margin-bottom: 20px;
        }
        
        /* Black square styling for amounts */
        .amount {
            background-color: #000;
            color: #fff;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Invoice Header -->
    <div class="invoice-header">
        <h1>INVOICE</h1>
        <div class="invoice-info">
            <strong>Invoice No.:</strong> {{ $invoiceNumber }}<br>
            <strong>Issue Date:</strong> {{ $issueDate }}<br>
            <strong>Due Date:</strong> {{ $dueDate }}
        </div>
    </div>

    <!-- Resort Information -->
    <div class="section clearfix">
        <div class="resort-info">
            <div class="section-title">Resort Information</div>
            <strong>Mt. ClaRamuel Resort</strong><br>
            Narra Street, Brgy. Marana 3rd<br>
            Ilagan, 3300 Isabela, Philippines<br>
            Phone: +63 995 290 1333<br>
            Email: mtclaramuelresort@gmail.com
        </div>

        <!-- Guest Information -->
        <div class="guest-info">
            <div class="section-title">Billed To (Guest Information)</div>
            <strong>Name:</strong> {{ $booking->user->firstname }} {{ $booking->user->lastname }}<br>
            <strong>Email:</strong> {{ $booking->user->email }}<br>
            <strong>Phone:</strong> {{ $booking->user->phone }}
        </div>
    </div>

    <!-- Booking Details Table -->
    <div class="section">
        <table class="details-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th width="15%">Period</th>
                    <th width="20%">Unit Price</th>
                    <th width="20%">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $subtotal = 0;
                @endphp

                @foreach($booking->summaries as $summary)
                    @php
                        $checkin = \Carbon\Carbon::parse($summary->bookingDetails->first()->checkin_date);
                        $checkout = \Carbon\Carbon::parse($summary->bookingDetails->first()->checkout_date);
                        $nights = $checkin->diffInDays($checkout);
                        $facilityTotal = $summary->facility_price * $nights;
                        $subtotal += $facilityTotal;
                    @endphp
                    
                    <tr>
                        <td>
                            <strong>Room Type: {{ $summary->facility->name }}</strong><br>
                            <small>
                                Check-in: {{ $checkin->format('M j, Y') }} | 
                                Check-out: {{ $checkout->format('M j, Y') }} | 
                                {{ $nights }} night(s)
                            </small>
                            
                            <!-- Guest Details -->
                            @php
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
                            
                            @if($guestsForFacility->count() > 0)
                                <div style="margin-top: 8px; font-size: 11px;">
                                    <strong>Guests:</strong>
                                    @foreach($guestsForFacility as $guest)
                                        {{ $guest['quantity'] }} {{ $guest['type'] }}{{ !$loop->last ? ',' : '' }}
                                    @endforeach
                                    (Total: {{ $guestsForFacility->sum('quantity') }} guests)
                                </div>
                            @endif
                        </td>
                        <td class="text-center">{{ $nights }} Night</td>
                        <td class="text-right">₱{{ number_format($summary->facility_price, 2) }}</td>
                        <td class="text-right">
                            <span class="amount">₱{{ number_format($facilityTotal, 2) }}</span>
                        </td>
                    </tr>
                    
                    <!-- Breakfast Row -->
                    @if($summary->breakfast)
                        @php
                            $breakfastTotal = $summary->breakfast_price * $nights;
                            $subtotal += $breakfastTotal;
                        @endphp
                        <tr>
                            <td>
                                <strong>Breakfast Included</strong><br>
                                <small>For {{ $nights }} night(s) - {{ $summary->facility->name }}</small>
                            </td>
                            <td class="text-center">{{ $nights }} Breakfast</td>
                            <td class="text-right">₱{{ number_format($summary->breakfast_price, 2) }}</td>
                            <td class="text-right">
                                <span class="amount">₱{{ number_format($breakfastTotal, 2) }}</span>
                            </td>
                        </tr>
                    @endif
                @endforeach

                <!-- Addons -->
                @if($booking->guestAddons && $booking->guestAddons->count() > 0)
                    @foreach($booking->guestAddons as $addon)
                        @php
                            $subtotal += $addon->total_cost;
                        @endphp
                        <tr>
                            <td>
                                <strong>Additional Service: {{ $addon->type }}</strong>
                                @if($addon->quantity > 1)
                                    <br><small>Quantity: {{ $addon->quantity }}</small>
                                @endif
                            </td>
                            <td class="text-center">{{ $addon->quantity }}</td>
                            <td class="text-right">₱{{ number_format($addon->cost, 2) }}</td>
                            <td class="text-right">
                                <span class="amount">₱{{ number_format($addon->total_cost, 2) }}</span>
                            </td>
                        </tr>
                    @endforeach
                @endif

                <!-- Empty row for spacing -->
                <tr>
                    <td colspan="4" style="border: none; height: 20px;"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Totals Section -->
    <div class="totals-section">
        @php
            $taxes = 0;
            $totalAmount = $subtotal + $taxes;
            $advancePaid = $booking->payments->sum('amount');
            $balance = $totalAmount - $advancePaid;
        @endphp

        <div class="total-row">
            <span class="total-label">Subtotal:</span>
            <span class="total-value">
                <span class="amount">₱{{ number_format($subtotal, 2) }}</span>
            </span>
        </div>
        
        <!-- <div class="total-row">
            <span class="total-label">Taxes & Fees:</span>
            <span class="total-value">
                <span class="amount">₱{{ number_format($taxes, 2) }}</span>
            </span>
        </div> -->
        
        <div class="total-row grand-total">
            <span class="total-label">Total Amount:</span>
            <span class="total-value">
                <span class="amount">₱{{ number_format($totalAmount, 2) }}</span>
            </span>
        </div>
    </div>

    <!-- Payment Information -->
    <div class="payment-info">
        <div class="section-title">Payment Information</div>
        
        @if($booking->payments && $booking->payments->count() > 0)
            @php $payment = $booking->payments->first(); @endphp
            <div class="payment-details">
                <div class="payment-row">
                    <span class="payment-label">Payment Method:</span>
                    <span>{{ ucfirst($payment->method) }}</span>
                </div>
                
                <div class="payment-row">
                    <span class="payment-label">Reference No.:</span>
                    <span>{{ $payment->reference_no }}</span>
                </div>
                
                <div class="payment-row">
                    <span class="payment-label">Advance Paid:</span>
                    <span>
                        <span class="amount">₱{{ number_format($advancePaid, 2) }}</span>
                    </span>
                </div>
                
                <div class="payment-row">
                    <span class="payment-label">Balance Due:</span>
                    <span>
                        @if($balance > 0)
                            <span class="amount">₱{{ number_format($balance, 2) }}</span>
                        @else
                            <span class="amount">₱0.00 (Fully Paid)</span>
                        @endif
                    </span>
                </div>
                
                <div class="payment-row">
                    <span class="payment-label">Payment Date:</span>
                    <span>
                        {{ $payment && $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('F j, Y \a\t g:i A') : 'N/A' }}
                    </span>
                </div>
            </div>
        @else
            <div class="payment-row">
                <span class="payment-label">Payment Status:</span>
                <span>No payment information available</span>
            </div>
        @endif
    </div>

    @if($balance <= 0)
        <div class="thank-you">
            <strong>Thank you for your payment. This invoice is issued as proof of your booking transaction.</strong>
        </div>
    @endif

    <!-- Footer -->
    <div style="margin-top: 50px; font-size: 10px; color: #666; text-align: center; border-top: 1px solid #eee; padding-top: 10px;">
        <p>For inquiries, please contact: +63 995 290 1333 | mtclaramuelresort@gmail.com</p>
        <p>This is a computer-generated invoice. No signature required.</p>
    </div>
</body>
</html>