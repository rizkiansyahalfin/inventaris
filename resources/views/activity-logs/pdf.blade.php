<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Log Aktivitas</h2>
    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Pengguna</th>
                <th>Modul</th>
                <th>Aksi</th>
                <th>Deskripsi</th>
                <th>IP Address</th>
                <th>User Agent</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activityLogs as $log)
                <tr>
                    <td>{{ $log->created_at ? $log->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i:s') : '' }}</td>
                    <td>{{ $log->user->name ?? 'Sistem' }}</td>
                    <td>{{ $log->module }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->ip_address }}</td>
                    <td>{{ $log->user_agent }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 