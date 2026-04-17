<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Day Tour Financial Report</title>
    <style>
        /* DejaVu Sans is required for the Peso symbol (₱) to render correctly */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1f2937;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            color: #1e40af;
            margin: 0;
        }

        .meta {
            text-align: right;
            font-size: 9px;
            color: #6b7280;
        }

        .summary-box {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 25px;
            border: 1px solid #e5e7eb;
            display: table;
            width: 100%;
        }

        .summary-col {
            display: table-cell;
            width: 25%;
            text-align: center;
            border-right: 1px solid #d1d5db;
        }

        .summary-col:last-child {
            border-right: none;
        }

        .stat-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
        }

        .stat-sub {
            font-size: 9px;
            color: #6b7280;
            margin-top: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9px;
        }

        th {
            background-color: #2563eb;
            color: white;
            text-align: left;
            padding: 8px;
            text-transform: uppercase;
        }

        td {
            border-bottom: 1px solid #e5e7eb;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-green {
            color: #059669;
        }

        .text-blue {
            color: #2563eb;
        }

        .font-bold {
            font-weight: bold;
        }

        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-green {
            background: #d1fae5;
            color: #065f46;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #374151;
            margin: 20px 0 10px 0;
            border-left: 4px solid #2563eb;
            padding-left: 8px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h1>DAY TOUR REPORT</h1>
                    <div style="font-size: 11px; margin-top: 4px;">{{ config('app.name', 'Resort Name') }} Analytics
                    </div>
                </td>
                <td class="text-right">
                    <div class="meta">Generated: {{ $generatedAt->format('M d, Y h:i A') }}</div>
                    <div class="meta">Period: {{ $period }}</div>
                    <div class="meta">Filter: {{ $filterValue }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <div class="summary-col">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value text-green">₱{{ number_format($summary['totalIncome'], 2) }}</div>
            <div class="stat-sub">Confirmed Income</div>
        </div>
        <div class="summary-col">
            <div class="stat-label">Revenue Sources</div>
            <div class="stat-sub text-blue">Pool: ₱{{ number_format($summary['poolRevenue'], 2) }}</div>
            <div class="stat-sub text-green">Park: ₱{{ number_format($summary['parkRevenue'], 2) }}</div>
        </div>
        <div class="summary-col">
            <div class="stat-label">Volume</div>
            <div class="stat-value">{{ $summary['totalBookings'] }}</div>
            <div class="stat-sub">Total Bookings</div>
        </div>
        <div class="summary-col">
            <div class="stat-label">Footfall</div>
            <div class="stat-value">{{ $summary['totalGuests'] }}</div>
            <div class="stat-sub">Total Guests</div>
        </div>
    </div>

    <div class="section-title">DAILY REVENUE SUMMARY</div>
    <table>
        <thead>
            <tr>
                <th width="40%">Date</th>
                <th width="30%">Description</th>
                <th width="30%" class="text-right">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dailyStats as $stat)
                <tr>
                    <td>{{ $period }} {{ $stat['day'] }}</td>
                    <td>Daily Aggregated Sales</td>
                    <td class="text-right font-bold text-green">₱{{ number_format($stat['revenue'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">No revenue recorded for this period.</td>
                </tr>
            @endforelse
            <tr style="background-color: #e5e7eb; font-weight: bold;">
                <td>TOTAL</td>
                <td></td>
                <td class="text-right text-green">₱{{ number_format($summary['totalIncome'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">TRANSACTION DETAILS</div>
    <table>
        <thead>
            <tr>
                <th width="15%">Date</th>
                <th width="15%">Ref No.</th>
                <th width="25%">Customer</th>
                <th width="15%">Status</th>
                <th width="10%">Guests</th>
                <th width="20%" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $txn)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($txn['date'])->format('M d') }}</td>
                    <td style="font-family: monospace;">{{ $txn['reference'] }}</td>
                    <td>{{ Str::limit($txn['customer_name'], 20) }}</td>
                    <td><span class="badge badge-green">{{ ucfirst($txn['status']) }}</span></td>
                    <td class="text-center">{{ $txn['guest_count'] }}</td>
                    <td class="text-right font-bold">₱{{ number_format($txn['amount'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No transactions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated by System | Confidential Financial Report | Page
        <script type="text/php">if ( isset($pdf) ) { echo $pdf->get_page_number(); }</script>
    </div>
</body>

</html>