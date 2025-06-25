@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Riwayat Pemeliharaan</h1>
        <div class="flex space-x-3">
            <a href="{{ route('maintenances.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                Tambah Data
            </a>
            <a href="{{ route('maintenances.export.pdf', request()->query()) }}" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                Export PDF
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white shadow-sm rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('maintenances.index') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Judul atau nama barang..." class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Semua Status</option>
                    <option value="ongoing" @selected(request('status') == 'ongoing')>Berlangsung</option>
                    <option value="completed" @selected(request('status') == 'completed')>Selesai</option>
                </select>
            </div>
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                <select id="type" name="type" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Semua Tipe</option>
                    <option value="Perawatan" @selected(request('type') == 'Perawatan')>Perawatan</option>
                    <option value="Perbaikan" @selected(request('type') == 'Perbaikan')>Perbaikan</option>
                    <option value="Penggantian" @selected(request('type') == 'Penggantian')>Penggantian</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm">
                    Filter
                </button>
                <a href="{{ route('maintenances.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 text-sm">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Section -->
    <div class="mb-4 text-sm text-gray-600">
        <p>Menampilkan {{ $maintenances->count() }} dari {{ $maintenances->total() }} data pemeliharaan</p>
        @if(request('search') || request('status') || request('type'))
            <p class="mt-1">
                Filter aktif: 
                @if(request('search'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1">
                        Pencarian: "{{ request('search') }}"
                    </span>
                @endif
                @if(request('status'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-1">
                        Status: {{ request('status') === 'completed' ? 'Selesai' : 'Berlangsung' }}
                    </span>
                @endif
                @if(request('type'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mr-1">
                        Tipe: {{ request('type') }}
                    </span>
                @endif
            </p>
        @endif
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Detail</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($maintenances as $maintenance)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <a href="{{ route('items.show', $maintenance->item) }}" class="text-indigo-600 hover:text-indigo-900">{{ $maintenance->item->name }}</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $maintenance->type }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $maintenance->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $maintenance->start_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($maintenance->is_completed)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Berlangsung
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $maintenance->cost ? 'Rp ' . number_format($maintenance->cost, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex space-x-2 justify-end">
                                    <a href="{{ route('maintenances.show', $maintenance) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                    <a href="{{ route('maintenances.edit', $maintenance) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Tidak ada data pemeliharaan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">
            {{ $maintenances->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection 