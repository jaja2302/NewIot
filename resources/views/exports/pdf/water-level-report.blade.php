<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Water Level Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .chart-container {
            text-align: center;
            margin: 20px 0;
        }

        .chart-image {
            max-width: 100%;
            height: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Water Level Report</h1>
        <p>Station: {{ $station }}</p>
        <p>Period: {{ $startDate }} {{ $endDate ? ' - ' . $endDate : '' }}</p>
        <p>Generated on: {{ now()->format('d F Y H:i:s') }}</p>
    </div>

    <div class="chart-container">
        @php
        $imagePath = public_path('storage/' . $imagePath);
        if (!file_exists($imagePath)) {
        throw new \Exception('Chart image not found');
        }
        @endphp
        <img src="{{ $imagePath }}" alt="Chart" style="max-width: 100%; height: auto;">
    </div>

    <h2>Data Details</h2>
    <table>
        <thead>
            <tr>
                <th>Date/Time</th>
                <th>Level Blok</th>
                <th>Level Parit</th>
                <th>Sensor Distance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                @if($period ==='Today')
                <td>{{ \Carbon\Carbon::parse($item['datetime'])->format('d M Y H:i:s') }}</td>
                @else
                <td>{{ \Carbon\Carbon::parse($item['datetime'])->format('d M Y') }}</td>
                @endif
                <td>{{ number_format($item['level_blok'], 2) }}</td>
                <td>{{ number_format($item['level_parit'], 2) }}</td>
                <td>{{ number_format($item['sensor_distance'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>© {{ date('Y') }} Water Level Monitoring System</p>
    </div>
</body>

</html>