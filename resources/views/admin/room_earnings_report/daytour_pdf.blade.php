<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Day Tour Report</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        .header { border-bottom: 2px solid #10b981; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #047857; margin: 0; font-size: 18px; }
        .meta { text-align: right; font-size: 9px; color: #666; }
        
        .summary-box { background: #ecfdf5; padding: 10px; border: 1px solid #d1fae5; margin-bottom: 20px; }
        .summary-table { width: 100%; text-align: center; }
        .stat-label { font-size: 8px; text-transform: uppercase; color: #059669; }
        .stat-val { font-size: 14px; font-weight: bold; color: #064e3b; }
        
        .section-title { font-weight: bold; font-size: 11px; margin: 15px 0 5px 0; color: #065f46; border-bottom: 1px solid #ddd; padding-bottom: 3px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background: #10b981; color: white; padding: 6px; text-align: left; font-size: 9px; }
        td { border-bottom: 1px solid #eee; padding: 6px; }
        .text-right { text-align: right; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <table width="100%">
            <tr>
                <td><h1>DAY TOUR ANALYTICS</h1></td>
                <td class="meta">
                    Generated: {{ $generatedAt->format('M d, Y H:i') }}<br>
                    Period: {{ $filter['month'] }} {{ $filter['year'] }}<br>
                    Category: {{ $filter['category'] }}
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td>
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-val">₱{{ number_format($stats['total_revenue'], 2) }}</div>
                </td>
                <td>
                    <div class="stat-label">Total Bookings</div>
                    <div class="stat-val">{{ $stats['total_bookings'] }}</div>
                </td>
                <td>
                    <div class="stat-label">Total Guests</div>
                    <div class="stat-val">{{ $stats['total_guests'] }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">BOOKING DETAILS LOG</div>
    <table>
        <thead>
            <tr>
                <th width="15%">Reference</th>
                <th width="15%">Date</th>
                <th width="20%">Customer</th>
                <th width="10%">Status</th>
                <th width="25%">Details (Facilities/Guests)</th>
                <th width="15%" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
            <tr>
                <td style="font-family: monospace;">{{ $booking['reference'] }}</td>
                <td>{{ $booking['date'] }}</td>
                <td>
                    <strong>{{ $booking['customer_name'] }}</strong><br>
                    <span style="color:#666;">{{ $booking['phone'] ?? '-' }}</span>
                </td>
                <td>{{ ucfirst($booking['status']) }}</td>
                <td>
                    @if(!empty($booking['facilities']))
                        <div style="margin-bottom:4px; font-weight:bold; font-size:8px;">Facilities:</div>
                        @foreach($booking['facilities'] as $f)
                            <div>• {{ $f['name'] }} (x{{ $f['quantity'] }})</div>
                        @endforeach
                    @endif
                    
                    @if(!empty($booking['guest_breakdown']))
                        <div style="margin-top:4px; font-weight:bold; font-size:8px;">Guests:</div>
                        @foreach($booking['guest_breakdown'] as $g)
                            <div>• {{ $g['type'] }} - {{ $g['location'] }} (x{{ $g['count'] }})</div>
                        @endforeach
                    @endif
                </td>
                <td class="text-right"><strong>₱{{ number_format($booking['amount'], 2) }}</strong></td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">No bookings found for this period.</td></tr>
            @endforelse
            
            <tr style="background-color: #f0fdf4; border-top: 2px solid #10b981;">
                <td colspan="5" style="text-align: right; font-weight: bold;">GRAND TOTAL</td>
                <td class="text-right" style="font-weight: bold; color: #047857;">₱{{ number_format($stats['total_revenue'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Confidential Report - Generated by System
    </div>
</body>
</html>