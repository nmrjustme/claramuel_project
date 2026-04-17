<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Detailed Financial Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #1f2937; }
        
        /* Header Section */
        .header { width: 100%; border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { font-size: 16px; color: #1e40af; margin: 0; }
        .header .meta { text-align: right; font-size: 8px; color: #6b7280; }

        /* Summary Cards */
        .summary-box { 
            background-color: #f3f4f6; padding: 10px; border-radius: 4px; margin-bottom: 20px; 
            display: table; width: 100%; border: 1px solid #e5e7eb;
        }
        .summary-col { display: table-cell; width: 25%; text-align: center; border-right: 1px solid #d1d5db; vertical-align: top; }
        .summary-col:last-child { border-right: none; }
        .stat-label { font-size: 8px; text-transform: uppercase; color: #6b7280; letter-spacing: 0.5px; margin-bottom: 2px;}
        .stat-value { font-size: 14px; font-weight: bold; margin-top: 4px; }
        .stat-sub { font-size: 8px; color: #6b7280; margin-top: 2px; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 8px; }
        th { background-color: #2563eb; color: white; text-align: left; padding: 6px; text-transform: uppercase; }
        td { border-bottom: 1px solid #e5e7eb; padding: 6px; vertical-align: middle; }
        tr:nth-child(even) { background-color: #f9fafb; }
        
        /* Helpers */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-green { color: #059669; }
        .text-blue { color: #2563eb; }
        .text-red { color: #dc2626; }
        .font-bold { font-weight: bold; }
        
        .badge { 
            padding: 2px 4px; border-radius: 3px; font-size: 7px; text-transform: uppercase; 
            display: inline-block; font-weight: bold;
        }
        .badge-green { background: #d1fae5; color: #065f46; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-red { background: #fee2e2; color: #991b1b; }

        /* Section Titles */
        .section-title { 
            font-size: 11px; font-weight: bold; color: #374151; margin-bottom: 5px; 
            border-left: 3px solid #2563eb; padding-left: 8px; margin-top: 15px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table style="width: 100%; margin: 0;">
            <tr>
                <td style="border:none; padding:0;">
                    <h1>FINANCIAL REPORT</h1>
                    <div style="font-size: 10px; margin-top: 2px;">{{ config('app.name') }} Accounting</div>
                </td>
                <td style="border:none; padding:0; text-align:right;">
                    <div class="meta">Generated: {{ $generatedAt->format('M d, Y h:i A') }}</div>
                    <div class="meta">Filter: {{ ucfirst($period) }} | {{ $filterValue ?: 'All' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <div class="summary-col">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value text-green">₱{{ number_format($summary['totalIncome'], 2) }}</div>
            <div class="stat-sub">Rooms + Day Tours</div>
        </div>
        <div class="summary-col">
            <div class="stat-label">Revenue Breakdown</div>
            <div class="stat-sub text-blue">Day Tours: ₱{{ number_format(collect($summary['combined'])->sum('daytour'), 2) }}</div>
            <div class="stat-sub text-green">Rooms: ₱{{ number_format(collect($summary['combined'])->sum('room'), 2) }}</div>
        </div>
        <div class="summary-col">
            <div class="stat-label">Total Expenses</div>
            <div class="stat-value text-red">₱{{ number_format($summary['totalExpense'], 2) }}</div>
            <div class="stat-sub">Operational</div>
        </div>
        <div class="summary-col">
            <div class="stat-label">Net Profit</div>
            <div class="stat-value {{ $summary['netTotal'] >= 0 ? 'text-green' : 'text-red' }}">
                ₱{{ number_format($summary['netTotal'], 2) }}
            </div>
        </div>
    </div>

    <div class="section-title">PERIOD BREAKDOWN SUMMARY</div>
    <table>
        <thead>
            <tr>
                <th width="20%">Period</th>
                <th width="16%" class="text-right">Room Rev</th>
                <th width="16%" class="text-right">Day Tour Rev</th>
                <th width="16%" class="text-right">Expenses</th>
                <th width="16%" class="text-right">Total Rev</th>
                <th width="16%" class="text-right">Net Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summary['combined'] as $row)
                <tr>
                    <td>{{ $row['label'] }}</td>
                    <td class="text-right text-green">₱{{ number_format($row['room'], 2) }}</td>
                    <td class="text-right text-blue">₱{{ number_format($row['daytour'], 2) }}</td>
                    <td class="text-right text-red">₱{{ number_format($row['expense'], 2) }}</td>
                    <td class="text-right font-bold">₱{{ number_format($row['income'], 2) }}</td>
                    <td class="text-right font-bold {{ $row['net'] >= 0 ? 'text-green' : 'text-red' }}">
                        ₱{{ number_format($row['net'], 2) }}
                    </td>
                </tr>
            @endforeach
            <tr style="background-color: #e5e7eb; border-top: 2px solid #374151;">
                <td class="font-bold">GRAND TOTAL</td>
                <td class="text-right font-bold text-green">₱{{ number_format(collect($summary['combined'])->sum('room'), 2) }}</td>
                <td class="text-right font-bold text-blue">₱{{ number_format(collect($summary['combined'])->sum('daytour'), 2) }}</td>
                <td class="text-right font-bold text-red">₱{{ number_format($summary['totalExpense'], 2) }}</td>
                <td class="text-right font-bold">₱{{ number_format($summary['totalIncome'], 2) }}</td>
                <td class="text-right font-bold {{ $summary['netTotal'] >= 0 ? 'text-green' : 'text-red' }}">
                    ₱{{ number_format($summary['netTotal'], 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">DETAILED TRANSACTION LOG</div>
    <table>
        <thead>
            <tr>
                <th width="12%">Date</th>
                <th width="10%">Type</th>
                <th width="12%">Reference</th>
                <th width="18%">Customer / Payee</th>
                <th width="23%">Description</th>
                <th width="10%">Method</th>
                <th width="15%" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $txn)
                <tr>
                    <td>{{ $txn['date'] }}</td>
                    <td>
                        @if($txn['color'] == 'green')
                            <span class="badge badge-green">{{ $txn['type'] }}</span>
                        @elseif($txn['color'] == 'blue')
                            <span class="badge badge-blue">{{ $txn['type'] }}</span>
                        @else
                            <span class="badge badge-red">{{ $txn['type'] }}</span>
                        @endif
                    </td>
                    <td style="font-family: monospace;">{{ $txn['reference'] }}</td>
                    <td class="font-bold">{{ Str::limit($txn['customer'], 20) }}</td>
                    <td>{{ Str::limit($txn['description'], 35) }}</td>
                    <td>{{ $txn['method'] }}</td>
                    <td class="text-right {{ $txn['amount'] < 0 ? 'text-red' : 'text-green font-bold' }}">
                        {{ $txn['amount'] < 0 ? '-' : '' }}₱{{ number_format(abs($txn['amount']), 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 15px;">No transactions found for this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 7px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 5px;">
        Generated by System | Page <script type="text/php">echo $PAGE_NUM . " of " . $PAGE_COUNT;</script>
    </div>
</body>
</html>