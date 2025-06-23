@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl px-4 py-8">
    <a href="{{ route('activity-logs.index') }}" class="text-sm text-blue-600 hover:underline mb-6 inline-block">&larr; Kembali ke Log Aktivitas</a>
    <div class="bg-white border rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Detail Log Aktivitas</h1>
        <p class="text-sm text-gray-500 mb-6">Informasi lengkap aktivitas yang tercatat di sistem.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 mb-6">
            <div>
                <div class="text-xs text-gray-500 mb-1">Waktu</div>
                <div class="text-base text-gray-900">{{ $log->created_at ? $log->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i:s') : '-' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 mb-1">Pengguna</div>
                <div class="text-base text-gray-900">{{ $log->user->name ?? 'Sistem' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 mb-1">Modul</div>
                <div class="text-base text-gray-900">{{ $log->module }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 mb-1">Aksi</div>
                <div class="text-base text-gray-900">{{ $log->action }}</div>
            </div>
        </div>
        <div class="mb-6">
            <div class="text-xs text-gray-500 mb-1">Deskripsi</div>
            <div class="text-sm text-gray-800 whitespace-pre-line bg-gray-50 rounded p-3 border">{{ $log->description }}</div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <div class="text-xs text-gray-500 mb-1">IP Address</div>
                <div class="text-sm text-gray-900">{{ $log->ip_address }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 mb-1">User Agent</div>
                <div class="text-xs text-gray-700 font-mono break-all">{{ $log->user_agent }}</div>
            </div>
            @if($geo)
            <div class="md:col-span-2">
                <div class="text-xs text-gray-500 mb-1">Lokasi (GeoIP)</div>
                <div class="text-xs text-gray-900">
                    {{ $geo['city'] ?? '-' }}, {{ $geo['region'] ?? '-' }}, {{ $geo['country_name'] ?? '-' }}<br>
                    <span class="text-gray-500">Lat: {{ $geo['latitude'] ?? '-' }}, Lon: {{ $geo['longitude'] ?? '-' }}</span>
                </div>
            </div>
            @endif
        </div>
        @if($before || $after)
        <div class="mt-8">
            <div class="text-sm font-semibold text-gray-700 mb-2">Perubahan Data</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-xs text-gray-500 mb-1">Sebelum</div>
                    <pre class="bg-gray-50 p-2 rounded text-xs text-gray-700 border">{{ json_encode($before, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Sesudah</div>
                    <pre class="bg-gray-50 p-2 rounded text-xs text-gray-700 border">{{ json_encode($after, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 