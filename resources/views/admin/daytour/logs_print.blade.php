<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Receipt #{{ $log->id }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.3;
            color: #374151;
            background: white;
            padding: 10px;
            font-size: 11px;
            max-width: 190mm;
            margin: 0 auto;
        }
        
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
            
            body {
                padding: 0;
                font-size: 10px;
                max-width: none;
            }
            
            .no-print {
                display: none !important;
            }
            
            /* Force single page */
            .receipt-container {
                height: auto !important;
                max-height: 270mm !important;
                overflow: hidden !important;
            }
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #dc2626;
        }
        
        .logo-container {
            margin-bottom: 8px;
        }
        
        .logo {
            height: 60px;
            width: auto;
            object-fit: contain;
        }
        
        .receipt-header h1 {
            font-size: 16px;
            font-weight: 700;
            color: #dc2626;
            margin-bottom: 3px;
        }
        
        .receipt-header p {
            font-size: 10px;
            color: #6b7280;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 12px;
        }
        
        .info-card {
            padding: 8px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            background: #f8fafc;
        }
        
        .info-card h3 {
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #dc2626;
            font-size: 11px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 10px;
        }
        
        .badge {
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: 500;
        }
        
        .badge-paid { background: #10b981; color: white; }
        .badge-pending { background: #f59e0b; color: white; }
        .badge-approved { background: #3b82f6; color: white; }
        
        /* Ultra-compact pricing table */
        .pricing-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 10px;
        }
        
        .pricing-table th {
            background: #dc2626;
            color: white;
            padding: 4px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
        }
        
        .pricing-table td {
            padding: 3px 4px;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .pricing-table tr:last-child td {
            border-bottom: none;
        }
        
        .area-header {
            background: #f8fafc;
            font-weight: 600;
            color: #374151;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .total-section {
            margin-top: 8px;
            padding: 8px;
            background: #dc2626;
            color: white;
            border-radius: 4px;
            text-align: center;
        }
        
        .total-section .label {
            font-size: 11px;
            font-weight: 600;
        }
        
        .total-section .amount {
            font-size: 14px;
            font-weight: 700;
        }
        
        .footer {
            text-align: center;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 9px;
        }
        
        .no-print {
            text-align: center;
            margin-bottom: 15px;
        }
        
        .btn {
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin: 0 4px;
            font-size: 11px;
            border: 1px solid #d1d5db;
        }
        
        .btn-primary {
            background: #3b82f6;
            color: white;
            border: none;
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
            border: none;
        }
        
        /* Compact utility classes */
        .compact { padding: 2px; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .py-1 { padding-top: 4px; padding-bottom: 4px; }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print mr-1"></i>Print
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times mr-1"></i>Close
        </button>
    </div>

    <div class="receipt-container">
        <!-- Header with Logo -->
        <div class="receipt-header">
            <div class="logo-container">
                <img src="{{ asset('images/logo.png') }}" alt="Day Tour Resort Logo" class="logo" onerror="this.style.display='none'">
            </div>
            <h1>Mt.Claramuel Resort And Events Place</h1>
            <p>Receipt #{{ $log->id }} • {{ \Carbon\Carbon::now()->format('M d, Y') }}</p>
        </div>

        <!-- Information Grid -->
        <div class="info-grid">
            <!-- Booking Info -->
            <div class="info-card">
                <h3>Booking Details</h3>
                <div class="info-row">
                    <span>Date:</span>
                    <span>{{ \Carbon\Carbon::parse($log->date_tour)->format('M d, Y') }}</span>
                </div>
                <div class="info-row">
                    <span>Status:</span>
                    <span class="badge badge-{{ $log->status }}">{{ ucfirst($log->status) }}</span>
                </div>
                <div class="info-row">
                    <span>Service:</span>
                    <span>{{ $serviceType['type'] }}</span>
                </div>
            </div>

            <!-- Guest Info -->
            <div class="info-card">
                <h3>Guest Information</h3>
                <div class="info-row">
                    <span>Name:</span>
                    <span>{{ $log->user->firstname }} {{ $log->user->lastname }}</span>
                </div>
                <div class="info-row">
                    <span>Contact:</span>
                    <span>{{ $log->user->phone }}</span>
                </div>
                <div class="info-row">
                    <span>Total Guests:</span>
                    <span>{{ $serviceType['total'] }}</span>
                </div>
            </div>
        </div>

        <!-- Pricing Breakdown -->
        <div class="info-card">
            <h3>Rate Breakdown</h3>
            
            <table class="pricing-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Rate</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Pool Area -->
                    @if(count($pricingDetails['pool_details']) > 0)
                    <tr class="area-header">
                        <td colspan="4">Pool Area</td>
                    </tr>
                    @foreach($pricingDetails['pool_details'] as $type => $detail)
                    <tr>
                        <td>{{ $type }}</td>
                        <td class="text-center">{{ $detail['quantity'] }}</td>
                        <td class="text-center">₱{{ number_format($detail['rate'], 0) }}</td>
                        <td class="text-right">₱{{ number_format($detail['total'], 0) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="3" class="text-right"><strong>Pool Subtotal:</strong></td>
                        <td class="text-right"><strong>₱{{ number_format($pricingDetails['pool_subtotal'], 0) }}</strong></td>
                    </tr>
                    @endif

                    <!-- Park Area -->
                    @if(count($pricingDetails['park_details']) > 0)
                    <tr class="area-header">
                        <td colspan="4">Park Area</td>
                    </tr>
                    @foreach($pricingDetails['park_details'] as $type => $detail)
                    <tr>
                        <td>{{ $type }}</td>
                        <td class="text-center">{{ $detail['quantity'] }}</td>
                        <td class="text-center">₱{{ number_format($detail['rate'], 0) }}</td>
                        <td class="text-right">₱{{ number_format($detail['total'], 0) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="3" class="text-right"><strong>Park Subtotal:</strong></td>
                        <td class="text-right"><strong>₱{{ number_format($pricingDetails['park_subtotal'], 0) }}</strong></td>
                    </tr>
                    @endif

                    <!-- Accommodations -->
                    @if(count($pricingDetails['accommodation_details']) > 0)
                    <tr class="area-header">
                        <td colspan="4">Accommodations</td>
                    </tr>
                    @foreach($pricingDetails['accommodation_details'] as $facility => $detail)
                    <tr>
                        <td>{{ $facility }}</td>
                        <td class="text-center">{{ $detail['quantity'] }}</td>
                        <td class="text-center">₱{{ number_format($detail['rate'], 0) }}</td>
                        <td class="text-right">₱{{ number_format($detail['total'], 0) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="3" class="text-right"><strong>Accommodation Subtotal:</strong></td>
                        <td class="text-right"><strong>₱{{ number_format($pricingDetails['accommodation_subtotal'], 0) }}</strong></td>
                    </tr>
                    @endif

                    <!-- Additional Charges -->
                    <tr class="area-header">
                        <td colspan="4">Additional Charges</td>
                    </tr>
                    <tr>
                        <td>Service Fee</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-right">₱0</td>
                    </tr>
                    <tr>
                        <td>Processing Fee</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-right">₱0</td>
                    </tr>

                    <!-- Taxes -->
                    <tr class="area-header">
                        <td colspan="4">Taxes & Fees</td>
                    </tr>
                    <tr>
                        <td>VAT (0%)</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-right">₱0</td>
                    </tr>
                    <tr>
                        <td>Local Tax</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-right">₱0</td>
                    </tr>
                </tbody>
            </table>

            <!-- Grand Total -->
            <div class="total-section">
                <div class="label">GRAND TOTAL</div>
                <div class="amount">₱{{ number_format($pricingDetails['total_amount'], 0) }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for choosing Mt.Claramuel Resort And Events place!</p>
            <p>+63 995 290 1333 • mtclaramuelresort@gmail.com</p>
            <p>Computer-generated receipt • Valid without signature</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            if (!window.location.hash.includes('no-print')) {
                setTimeout(() => {
                    window.print();
                }, 100);
            }
        }
        
        if (window.location.hash !== '#no-print') {
            window.location.hash = 'no-print';
        }
    </script>
</body>
</html>