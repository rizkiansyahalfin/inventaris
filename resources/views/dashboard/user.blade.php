<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistik Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Peminjaman Aktif</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['active_borrows'] }}</div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Peminjaman</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_borrows'] }}</div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Permintaan Pending</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['pending_requests'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Peminjaman Aktif -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Peminjaman Aktif</h3>
                    <div class="overflow-x-auto">
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $borrow->item->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $borrow->created_at->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $borrow->due_date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($borrow->due_date->isPast())
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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Riwayat Peminjaman</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Barang</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal Pinjam</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal Kembali</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($borrowHistory as $borrow)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $borrow->item->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $borrow->created_at->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $borrow->returned_at ? $borrow->returned_at->format('d/m/Y') : '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($borrow->status === 'returned')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Dikembalikan
                                            </span>
                                        @elseif($borrow->status === 'rejected')
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

            <!-- Notifikasi -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Notifikasi</h3>
                    <div class="space-y-4">
                        @foreach($notifications as $notification)
                        <div class="flex items-start p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-shrink-0">
                                @if($notification->type === 'borrow_approved')
                                    <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @elseif($notification->type === 'borrow_rejected')
                                    <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @else
                                    <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @endif
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $notification->title }}
                                </p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $notification->message }}
                                </p>
                                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 