@extends('layouts.app')

@section('content')
    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-2">
                    <a href="{{ route('items.index') }}" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:underline">Total Barang</a>
                    <a href="{{ route('items.index') }}" class="ml-2 text-blue-500 hover:underline text-xs flex items-center" title="Lihat Barang">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14m-7 7h7a2 2 0 002-2v-7" /></svg>
                    </a>
                </div>
                <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_items'] }}</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-2">
                    <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:underline">Total Pengguna</a>
                    <a href="{{ route('admin.users.index') }}" class="ml-2 text-blue-500 hover:underline text-xs flex items-center" title="Lihat Pengguna">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14m-7 7h7a2 2 0 002-2v-7" /></svg>
                    </a>
                </div>
                <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_users'] }}</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-2">
                    <a href="{{ route('borrows.index') }}" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:underline">Total Peminjaman</a>
                    <a href="{{ route('borrows.index') }}" class="ml-2 text-blue-500 hover:underline text-xs flex items-center" title="Lihat Peminjaman">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14m-7 7h7a2 2 0 002-2v-7" /></svg>
                    </a>
                </div>
                <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_borrows'] }}</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-2">
                    <a href="{{ route('borrows.index', ['approval_status' => 'pending']) }}" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:underline">Permintaan Pending</a>
                    <a href="{{ route('borrows.index', ['approval_status' => 'pending']) }}" class="ml-2 text-blue-500 hover:underline text-xs flex items-center" title="Lihat Pending">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14m-7 7h7a2 2 0 002-2v-7" /></svg>
                    </a>
                </div>
                <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['pending_requests'] }}</div>
            </div>
        </div>
    </div>

    <!-- Grafik dan Tabel -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Grafik Peminjaman per Bulan -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <a href="{{ route('borrows.index') }}" class="text-lg font-medium text-gray-900 dark:text-gray-100 hover:underline">Peminjaman per Bulan</a>
                    <a href="{{ route('borrows.index') }}" class="ml-2 text-blue-500 hover:underline text-sm flex items-center" title="Lihat Detail">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14m-7 7h7a2 2 0 002-2v-7" /></svg>
                    </a>
                </div>
                <div class="relative h-[300px]">
                    <canvas id="borrowsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Barang per Kategori -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <a href="{{ route('items.index') }}" class="text-lg font-medium text-gray-900 dark:text-gray-100 hover:underline">Barang per Kategori</a>
                    <a href="{{ route('items.index') }}" class="ml-2 text-blue-500 hover:underline text-sm flex items-center" title="Lihat Detail">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14m-7 7h7a2 2 0 002-2v-7" /></svg>
                    </a>
                </div>
                <div class="relative h-[300px]">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel dan Notifikasi -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Peminjaman yang Perlu Persetujuan -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <a href="{{ route('borrows.index', ['approval_status' => 'pending']) }}" class="text-lg font-medium text-gray-900 dark:text-gray-100 hover:underline">Peminjaman yang Perlu Persetujuan</a>
                    <a href="{{ route('borrows.index', ['approval_status' => 'pending']) }}" class="ml-2 text-blue-500 hover:underline text-sm flex items-center" title="Lihat Semua">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14m-7 7h7a2 2 0 002-2v-7" /></svg>
                    </a>
                </div>
                <div class="overflow-x-auto" style="max-height: 300px; overflow-y: auto; min-height: 60px;">
                    @if($pendingApprovals->count() > 5)
                        <small class="text-gray-400">Scroll ke bawah untuk melihat semua data</small>
                    @endif
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pengguna</th>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Barang</th>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($pendingApprovals as $borrow)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $borrow->user?->name ?? 'User tidak ditemukan' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $borrow->item?->name ?? 'Barang tidak ditemukan' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $borrow->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    <form action="{{ route('borrows.approve', $borrow) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-2" onclick="return confirm('Setujui peminjaman ini?')">Setujui</button>
                                    </form>
                                    <form action="{{ route('borrows.reject', $borrow) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="rejection_reason" value="Ditolak oleh admin dari dashboard">
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('Tolak peminjaman ini?')">Tolak</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Stok Barang Kritis -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <a href="{{ route('items.index', ['stock' => 'low']) }}" class="text-lg font-medium text-gray-900 dark:text-gray-100 hover:underline">Stok Barang Kritis</a>
                    <a href="{{ route('items.index', ['stock' => 'low']) }}" class="ml-2 text-blue-500 hover:underline text-sm flex items-center" title="Lihat Semua">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14m-7 7h7a2 2 0 002-2v-7" /></svg>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Barang</th>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stok</th>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kategori</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($lowStockItems as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $item->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $item->stock }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $item->category?->name ?? 'Tidak ada kategori' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data untuk grafik peminjaman per bulan
    const borrowsPerMonth = @json($borrowsPerMonth);
    const borrowsLabels = Object.keys(borrowsPerMonth);
    const borrowsData = Object.values(borrowsPerMonth);

    // Data untuk grafik barang per kategori
    const itemsByCategory = @json($itemsByCategory);
    const categoryLabels = Object.keys(itemsByCategory);
    const categoryData = Object.values(itemsByCategory);

    // Chart.js Theme Detection and Configuration
    const isDarkMode = document.documentElement.classList.contains('dark');
    const chartTextColor = isDarkMode ? '#e5e7eb' : '#374151';
    const chartGridColor = isDarkMode ? 'rgba(75, 85, 99, 0.3)' : 'rgba(209, 213, 219, 0.3)';

    // Set Chart.js defaults
    Chart.defaults.color = chartTextColor;
    Chart.defaults.borderColor = chartGridColor;

    // Grafik peminjaman per bulan
    const borrowsChart = new Chart(
        document.getElementById('borrowsChart'),
        {
            type: 'line',
            data: {
                labels: borrowsLabels,
                datasets: [{
                    label: 'Jumlah Peminjaman',
                    data: borrowsData,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1,
                    fill: true,
                    backgroundColor: 'rgba(75, 192, 192, 0.1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: chartGridColor
                        },
                        ticks: {
                            color: chartTextColor
                        }
                    },
                    x: {
                        grid: {
                            color: chartGridColor
                        },
                        ticks: {
                            color: chartTextColor
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: chartTextColor
                        }
                    },
                    title: {
                        display: true,
                        text: 'Peminjaman per Bulan',
                        color: chartTextColor
                    }
                }
            }
        }
    );

    // Grafik barang per kategori
    const categoryChart = new Chart(
        document.getElementById('categoryChart'),
        {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)'
                    ],
                    borderColor: isDarkMode ? '#1f2937' : '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: chartTextColor,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Jumlah Barang per Kategori',
                        color: chartTextColor,
                        font: {
                            size: 16
                        }
                    }
                }
            }
        }
    );
</script>
@endpush 