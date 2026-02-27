@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg transition-colors duration-200">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Laporan dan Statistik</h2>
                    {{-- Rest of the content stays inside the p-6 div --}}


                    <!-- Filter Section -->
                    <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-900/50 border dark:border-gray-700 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Filter Laporan</h3>
                        <form action="{{ route('reports.index') }}" method="GET" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="category"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</label>
                                    <select name="category" id="category"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Semua Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="status"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <select name="status" id="status"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Semua Status</option>
                                        <option value="Tersedia" {{ request('status') == 'Tersedia' ? 'selected' : '' }}>
                                            Tersedia</option>
                                        <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>
                                            Dipinjam</option>
                                        <option value="Rusak" {{ request('status') == 'Rusak' ? 'selected' : '' }}>Rusak
                                        </option>
                                        <option value="Hilang" {{ request('status') == 'Hilang' ? 'selected' : '' }}>Hilang
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label for="date_range"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rentang
                                        Tanggal</label>
                                    <div class="mt-1 flex space-x-2">
                                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                        </path>
                                    </svg>
                                    Terapkan Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Export Buttons -->
                    <div class="mb-8 flex flex-wrap gap-4">
                        <a href="{{ route('reports.export', ['format' => 'pdf'] + request()->query()) }}"
                            class="inline-flex items-center px-4 py-2 bg-rose-600 dark:bg-rose-500 text-white font-medium rounded-lg hover:bg-rose-700 dark:hover:bg-rose-600 transition-colors shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Export PDF
                        </a>
                        <a href="{{ route('reports.export', ['format' => 'excel'] + request()->query()) }}"
                            class="inline-flex items-center px-4 py-2 bg-emerald-600 dark:bg-emerald-500 text-white font-medium rounded-lg hover:bg-emerald-700 dark:hover:bg-emerald-600 transition-colors shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Export Excel
                        </a>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        <div
                            class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow-sm border dark:border-gray-700 transition-colors">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Barang</h4>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalItems }}</p>
                        </div>
                        <div
                            class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow-sm border dark:border-gray-700 transition-colors">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Barang Tersedia</h4>
                            <p class="text-2xl font-semibold text-green-600 dark:text-green-400">{{ $availableItems }}</p>
                        </div>
                        <div
                            class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow-sm border dark:border-gray-700 transition-colors">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Barang Rusak/Hilang</h4>
                            <p class="text-2xl font-semibold text-red-600 dark:text-red-400">{{ $damagedItems }}</p>
                        </div>
                        <div
                            class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow-sm border dark:border-gray-700 transition-colors">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Transaksi</h4>
                            <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $totalTransactions }}</p>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div
                            class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow-sm border dark:border-gray-700 transition-colors">
                            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Statistik per Kategori
                            </h3>
                            <div class="relative h-[300px]">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                        <div
                            class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow-sm border dark:border-gray-700 transition-colors">
                            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Statistik Status Barang
                            </h3>
                            <div class="relative h-[300px]">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 overflow-hidden transition-colors">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Kode</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Nama</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Kategori</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Kondisi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $item->code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $item->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $item->category?->name ?? 'Tidak ada kategori' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $item->status == 'Tersedia' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                        {{ $item->status == 'Dipinjam' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                        {{ $item->status == 'Rusak' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                        {{ $item->status == 'Hilang' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $item->condition }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

            @push('scripts')
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    // Chart.js Theme Detection and Configuration
                    const isDarkMode = document.documentElement.classList.contains('dark');
                    const chartTextColor = isDarkMode ? '#e5e7eb' : '#374151';
                    const chartGridColor = isDarkMode ? 'rgba(75, 85, 99, 0.3)' : 'rgba(209, 213, 219, 0.3)';

                    // Set Chart.js defaults
                    Chart.defaults.color = chartTextColor;
                    Chart.defaults.borderColor = chartGridColor;

                    // Category Chart
                    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
                    new Chart(categoryCtx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($categoryStats->pluck('name')) !!},
                            datasets: [{
                                label: 'Jumlah Barang per Kategori',
                                data: {!! json_encode($categoryStats->pluck('count')) !!},
                                backgroundColor: isDarkMode ? 'rgba(129, 140, 248, 0.2)' : 'rgba(79, 70, 229, 0.2)',
                                borderColor: isDarkMode ? 'rgba(129, 140, 248, 1)' : 'rgba(79, 70, 229, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: chartGridColor },
                                    ticks: { color: chartTextColor }
                                },
                                x: {
                                    grid: { color: chartGridColor },
                                    ticks: { color: chartTextColor }
                                }
                            },
                            plugins: {
                                legend: { labels: { color: chartTextColor } }
                            }
                        }
                    });

                    // Status Chart
                    const statusCtx = document.getElementById('statusChart').getContext('2d');
                    new Chart(statusCtx, {
                        type: 'pie',
                        data: {
                            labels: {!! json_encode($statusStats->pluck('status')) !!},
                            datasets: [{
                                data: {!! json_encode($statusStats->pluck('count')) !!},
                                backgroundColor: [
                                    'rgba(34, 197, 94, 0.8)',
                                    'rgba(234, 179, 8, 0.8)',
                                    'rgba(239, 68, 68, 0.8)',
                                    'rgba(59, 130, 246, 0.8)'
                                ],
                                borderColor: isDarkMode ? '#111827' : '#ffffff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { color: chartTextColor }
                                }
                            }
                        }
                    });
                </script>
            @endpush
@endsection