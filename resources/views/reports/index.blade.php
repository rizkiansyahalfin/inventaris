@extends('layouts.app')

@section('content')
<div class="bg-white overflow-hidden shadow-sm rounded-lg">
    <div class="p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Laporan dan Statistik</h2>

        <!-- Filter Section -->
        <div class="mb-8 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-medium text-gray-700 mb-4">Filter Laporan</h3>
            <form action="{{ route('reports.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Kategori</label>
                        <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Semua Status</option>
                            <option value="Tersedia" {{ request('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                            <option value="Rusak" {{ request('status') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="Hilang" {{ request('status') == 'Hilang' ? 'selected' : '' }}>Hilang</option>
                        </select>
                    </div>

                    <div>
                        <label for="date_range" class="block text-sm font-medium text-gray-700">Rentang Tanggal</label>
                        <div class="mt-1 flex space-x-2">
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Export Buttons -->
        <div class="mb-8 flex space-x-4">
            <a href="{{ route('reports.export', ['format' => 'pdf'] + request()->query()) }}" 
               class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                Export PDF
            </a>
            <a href="{{ route('reports.export', ['format' => 'excel'] + request()->query()) }}" 
               class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                Export Excel
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-4 rounded-lg shadow">
                <h4 class="text-sm font-medium text-gray-500">Total Barang</h4>
                <p class="text-2xl font-semibold text-gray-900">{{ $totalItems }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h4 class="text-sm font-medium text-gray-500">Barang Tersedia</h4>
                <p class="text-2xl font-semibold text-green-600">{{ $availableItems }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h4 class="text-sm font-medium text-gray-500">Barang Rusak/Hilang</h4>
                <p class="text-2xl font-semibold text-red-600">{{ $damagedItems }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h4 class="text-sm font-medium text-gray-500">Total Transaksi</h4>
                <p class="text-2xl font-semibold text-blue-600">{{ $totalTransactions }}</p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-700 mb-4">Statistik per Kategori</h3>
                <canvas id="categoryChart"></canvas>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-700 mb-4">Statistik Status Barang</h3>
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($items as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->category?->name ?? 'Tidak ada kategori' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $item->status == 'Tersedia' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $item->status == 'Dipinjam' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $item->status == 'Rusak' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $item->status == 'Hilang' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->condition }}</td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($categoryStats->pluck('name')) !!},
            datasets: [{
                label: 'Jumlah Barang per Kategori',
                data: {!! json_encode($categoryStats->pluck('count')) !!},
                backgroundColor: 'rgba(79, 70, 229, 0.2)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
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
                    'rgba(34, 197, 94, 0.2)',
                    'rgba(234, 179, 8, 0.2)',
                    'rgba(239, 68, 68, 0.2)',
                    'rgba(59, 130, 246, 0.2)'
                ],
                borderColor: [
                    'rgba(34, 197, 94, 1)',
                    'rgba(234, 179, 8, 1)',
                    'rgba(239, 68, 68, 1)',
                    'rgba(59, 130, 246, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
@endpush
@endsection 