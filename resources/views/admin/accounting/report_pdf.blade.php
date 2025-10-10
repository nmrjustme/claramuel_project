<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Accounting Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size:12px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding:6px; text-align: left; }
        th { background:#f3f4f6; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h2>Accounting Report</h2>
    <p>Generated: {{ now()->format('M d, Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Period</th>
                <th class="right">Room</th>
                <th class="right">Day Tour</th>
                <th class="right">Event</th>
                <th class="right">Expenses</th>
                <th class="right">Net</th>
            </tr>
        </thead>
        <tbody>
            @foreach($combined as $row)
                <tr>
                    <td>{{ $row['label'] ?? '' }}</td>
                    <td class="right">₱{{ number_format($row['room'] ?? 0,2) }}</td>
                    <td class="right">₱{{ number_format($row['daytour'] ?? 0,2) }}</td>
                    <td class="right">₱{{ number_format($row['event'] ?? 0,2) }}</td>
                    <td class="right">₱{{ number_format($row['expense'] ?? 0,2) }}</td>
                    <td class="right">₱{{ number_format($row['net'] ?? 0,2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
