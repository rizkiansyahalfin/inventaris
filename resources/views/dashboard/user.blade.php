@extends('layouts.app')

@section('content')
    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-2">
                    <a href="{{ route('borrows.index', ['status' => 'borrowed']) }}" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:underline">Peminjaman Aktif</a>
                    <a href="{{ route('borrows.index', ['status' => 'borrowed']) }}" class="ml-2 text-blue-500 hover:underline text-xs flex items-center" title="Lihat Detail">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14m-7 7h7a2 2 0 002-2v-7" /></svg>
                    </a>
                </div>
                <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['active_borrows'] }}</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-2">
                    <a href="{{ route('borrows.index') }}" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:underline">Total Peminjaman</a>
                    <a href="{{ route('borrows.index') }}" class="ml-2 text-blue-500 hover:underline text-xs flex items-center" title="Lihat Detail">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14m-7 7h7a2 2 0 002-2v-7" /></svg>
                    </a>
                </div>
                <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_borrows'] }}</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-2">
                    <a href="{{ route('borrows.index', ['status' => 'pending']) }}" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:underline">Permintaan Pending</a>
                    <a href="{{ route('borrows.index', ['status' => 'pending']) }}" class="ml-2 text-blue-500 hover:underline text-xs flex items-center" title="Lihat Detail">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14m-7 7h7a2 2 0 002-2v-7" /></svg>
                    </a>
                </div>
                <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['pending_requests'] }}</div>
            </div>
        </div>
    </div>

    <!-- Tabel dan Notifikasi -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Peminjaman Aktif -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <a href="{{ route('borrows.index') }}" class="text-lg font-medium text-gray-900 dark:text-gray-100 hover:underline">Peminjaman Aktif</a>
                <a href="{{ route('borrows.index') }}" class="ml-2 text-blue-500 hover:underline text-sm flex items-center" title="Lihat Semua">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14m-7 7h7a2 2 0 002-2v-7" /></svg>
                </a>
            </div>
            <div class="overflow-x-auto" style="max-height: 300px; overflow-y: auto; min-height: 60px;">
                @if($activeBorrows->count() > 5)
                    <small class="text-gray-400">Scroll ke bawah untuk melihat semua data</small>
                @endif
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Barang</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal Pinjam</th>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jatuh Tempo</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($activeBorrows as $borrow)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $borrow->item?->name ?? 'Barang tidak ditemukan' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $borrow->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $borrow->due_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                @if($borrow->due_date < now())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    Terlambat
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Aktif
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Riwayat Peminjaman -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <a href="{{ route('borrows.index') }}" class="text-lg font-medium text-gray-900 dark:text-gray-100 hover:underline">Riwayat Peminjaman</a>
            <a href="{{ route('borrows.index') }}" class="ml-2 text-blue-500 hover:underline text-sm flex items-center" title="Lihat Semua">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14m-7 7h7a2 2 0 002-2v-7" /></svg>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Barang</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal Pinjam</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($borrowHistory as $borrow)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $borrow->item?->name ?? 'Barang tidak ditemukan' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $borrow->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                @if($borrow->status == 'returned')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    Dikembalikan
                                </span>
                                @elseif($borrow->status == 'rejected')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    Ditolak
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    {{ ucfirst($borrow->status) }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<!-- Notifikasi -->
@if($notifications->count() > 0)
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mt-6">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Notifikasi Terbaru</h3>
        <div class="space-y-4">
            @foreach($notifications as $notification)
            <div class="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $notification->message }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection 