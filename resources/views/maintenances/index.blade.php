@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Riwayat Pemeliharaan</h1>
        <a href="{{ route('maintenances.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
            Tambah Data
        </a>
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
                                {{ $maintenance->cost ? 'Rp ' . number_format($maintenance->cost, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('maintenances.show', $maintenance) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Tidak ada data pemeliharaan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">
            {{ $maintenances->links() }}
        </div>
    </div>
</div>
@endsection 