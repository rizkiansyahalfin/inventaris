<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Riwayat Pemeliharaan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
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
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .status-ongoing {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RIWAYAT PEMELIHARAAN</h1>
        <p>Sistem Inventaris</p>
        <p>Tanggal Export: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Barang</th>
                <th>Tipe</th>
                <th>Judul</th>
                <th>Tanggal Mulai</th>
                <th>Status</th>
                <th>Biaya</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maintenances as $index => $maintenance)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $maintenance->item->name }} ({{ $maintenance->item->code }})</td>
                    <td>{{ $maintenance->type }}</td>
                    <td>{{ $maintenance->title }}</td>
                    <td>{{ $maintenance->start_date->format('d/m/Y') }}</td>
                    <td>
                        @if($maintenance->is_completed)
                            <span class="status-completed">Selesai</span>
                        @else
                            <span class="status-ongoing">Berlangsung</span>
                        @endif
                    </td>
                    <td>{{ $maintenance->cost ? 'Rp ' . number_format($maintenance->cost, 0, ',', '.') : '-' }}</td>
                    <td>{{ $maintenance->user->name ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data pemeliharaan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Total Data: {{ $maintenances->count() }}</p>
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html> 