<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Petugas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistik Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Peminjaman Hari Ini</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['today_borrows'] }}</div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Pengembalian Hari Ini</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['today_returns'] }}</div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Pengembalian Tertunda</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['pending_returns'] }}</div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Stok Kritis</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['low_stock_items'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Peminjaman dan Pengembalian Hari Ini -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Peminjaman Hari Ini -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Peminjaman Hari Ini</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pengguna</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Barang</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($todayBorrows as $borrow)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $borrow->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $borrow->item->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $borrow->created_at->format('H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pengembalian Hari Ini -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Pengembalian Hari Ini</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pengguna</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Barang</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($todayReturns as $return)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $return->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $return->item->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $return->returned_at->format('H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengembalian yang Akan Datang -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Pengembalian yang Akan Datang (7 Hari)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pengguna</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Barang</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jatuh Tempo</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($upcomingReturns as $return)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $return->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $return->item->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $return->due_date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($return->due_date->isPast())
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
        </div>
    </div>
</x-app-layout> 