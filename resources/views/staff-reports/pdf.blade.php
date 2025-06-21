<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Staff</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .status-draft {
            background-color: #f3f4f6;
            color: #374151;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .status-submitted {
            background-color: #fef3c7;
            color: #92400e;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .status-reviewed {
            background-color: #d1fae5;
            color: #065f46;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .summary {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .summary h3 {
            margin-top: 0;
            color: #333;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN STAFF</h1>
        <p>Sistem Inventaris</p>
        <p>Tanggal Export: {{ date('d M Y H:i') }}</p>
    </div>

    @if($staffReports->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Petugas</th>
                    <th>Jam Kerja</th>
                    <th>Status</th>
                    <th>Reviewer</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staffReports as $index => $report)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $report->report_date->format('d M Y') }}</td>
                        <td>{{ $report->user->name }}</td>
                        <td>{{ $report->hours_worked }} jam</td>
                        <td>
                            <span class="status-{{ $report->status }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </td>
                        <td>{{ $report->reviewer ? $report->reviewer->name : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <h3>Ringkasan</h3>
            <p><strong>Total Laporan:</strong> {{ $staffReports->count() }}</p>
            <p><strong>Total Jam Kerja:</strong> {{ $staffReports->sum('hours_worked') }} jam</p>
            <p><strong>Status Draft:</strong> {{ $staffReports->where('status', 'draft')->count() }}</p>
            <p><strong>Status Diajukan:</strong> {{ $staffReports->where('status', 'submitted')->count() }}</p>
            <p><strong>Status Diulas:</strong> {{ $staffReports->where('status', 'reviewed')->count() }}</p>
        </div>
    @else
        <p>Tidak ada data laporan staff yang ditemukan.</p>
    @endif

    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh Sistem Inventaris</p>
        <p>© {{ date('Y') }} Sistem Inventaris. All rights reserved.</p>
    </div>
</body>
</html> 