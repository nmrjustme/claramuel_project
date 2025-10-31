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
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 25px;
            background: #fff;
        }

        /* Header Styles */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .logo-container {
            flex: 1;
        }

        .invoice-info {
            flex: 1;
            text-align: right;
        }

        /* Section Styles */
        .section {
            margin-bottom: 20px;
        }
        
        /* Flex container for Resort/Guest Info */
        .section.clearfix {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 30px; /* Added gap for better spacing */
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #555;
            border-bottom: 1px solid #eee;
            padding-bottom: 4px;
        }

        /* Resort & Guest Info */
        .resort-info,
        .guest-info {
            flex: 1;
            min-width: 0; /* Prevent flex items from overflowing */
        }
        
        .resort-info {
            padding-right: 15px;
        }
        
        .guest-info {
            padding-left: 15px;
        }
        
        /* Billed To Info */
        .billed-to strong {
            display: inline-block;
            min-width: 70px;
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
            padding: 10px 8px;
            border: 1px solid #ddd;
        }

        .details-table td {
            padding: 8px;
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
            margin-top: 15px;
            text-align: right;
            width: 300px;
            float: right;
        }

        .total-row {
            margin: 5px 0;
            display: flex;
            justify-content: space-between;
        }

        .total-label {
            font-weight: bold;
            min-width: 140px;
            text-align: right;
            padding-right: 10px;
        }

        .total-value {
            min-width: 110px;
            text-align: right;
        }

        .grand-total {
            font-size: 14px;
            font-weight: bold;
            border-top: 2px solid #333;
            padding-top: 8px;
            margin-top: 8px;
        }

        /* Payment Information */
        .payment-info {
            margin-top: 25px;
            padding: 12px;
            background-color: #f9f9f9;
            border-radius: 5px;
            clear: both;
        }

        .payment-details {
            margin-top: 8px;
        }

        .payment-row {
            display: flex;
            margin-bottom: 4px;
        }

        .payment-label {
            font-weight: bold;
            min-width: 110px;
        }

        /* Thank You Message */
        .thank-you {
            text-align: center;
            margin-top: 25px;
            font-style: italic;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        /* Utility Classes */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
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

        .logo-fallback {
            display: inline-block;
            padding: 20px 30px;
            border: 2px solid #333;
            font-weight: bold;
            text-align: center;
            background: #f5f5f5;
            font-size: 16px;
        }
        .resort-logo {
            max-height: 120px; /* Increased from 80px */
            max-width: 280px; /* Increased from 200px */
        }
        
        /* Visual separator between resort and guest info */
        .info-separator {
            width: 1px;
            background-color: #eee;
            align-self: stretch;
            margin: 0 15px;
        }
    </style>
</head>

<body>
    <div class="invoice-header">
        <div class="logo-container">
            @if(file_exists(public_path('imgs/logo.png')))
                <img src="{{ public_path('imgs/logo.png') }}" alt="Mt. ClaRamuel Resort Logo" class="resort-logo">
            @else
                <div class="logo-fallback">
                    Mt. ClaRamuel Resort<br>Logo
                </div>
            @endif
        </div>

        <div class="invoice-info">
            <div class="section-title">INVOICE</div>
            <strong>Invoice No.:</strong> {{ $invoiceNumber }}<br>
            <strong>Issue Date:</strong> {{ $issueDate }}<br>
            <strong>Due Date:</strong> {{ $dueDate }}
        </div>
    </div>

    <div class="section clearfix">
        <div class="resort-info">
            <div class="section-title">Resort Information</div>
            <strong>Mt. ClaRamuel Resort</strong><br>
            Narra Street, Brgy. Marana 3rd<br>
            Ilagan, 3300 Isabela, Philippines<br>
            TIN: 921-833-322-000 <br>
            Phone: +63 995 290 1333<br>
            Email: mtclaramuelresort@gmail.com
        </div>

        <div class="info-separator"></div>

        <div class="guest-info">
            <div class="section-title">Guest Information</div>
            <strong>Name:</strong> {{ $booking->user->firstname }} {{ $booking->user->lastname }}<br>
            <strong>Email:</strong> {{ $booking->user->email }}<br>
            <strong>Phone:</strong> {{ $booking->user->phone }}
        </div>
    </div>

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

                            @php
                                $guestsForFacility = $booking->guestDetails
                                    ->where('facility_id', $summary->facility_id)
                                    ->groupBy('guest_type_id')
                                    ->map(function ($items) {
                                        return [
                                            'type' => $items->first()->guestType->type ?? 'Unknown',
                                            'quantity' => $items->sum('quantity')
                                        ];
                                    });
                            @endphp

                            @if($guestsForFacility->count() > 0)
                                <div style="margin-top: 5px; font-size: 11px;">
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
                
                </tbody>
        </table>
    </div>

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

        <div class="total-row grand-total">
            <span class="total-label">Total Amount:</span>
            <span class="total-value">
                <span class="amount">₱{{ number_format($totalAmount, 2) }}</span>
            </span>
        </div>
    </div>

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

    <div
        style="margin-top: 30px; font-size: 10px; color: #666; text-align: center; border-top: 1px solid #eee; padding-top: 10px;"> <p>For inquiries, please contact: +63 995 290 1333 | mtclaramuelresort@gmail.com</p>
        <p>This is a computer-generated invoice. No signature required.</p>
    </div>
</body>

</html>