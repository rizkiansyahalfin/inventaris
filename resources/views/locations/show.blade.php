@extends('layouts.app')

@section('title', 'Detail Lokasi')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('locations.index') }}" class="text-blue-600 hover:text-blue-900 mr-4">
            ← Kembali
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Detail Lokasi</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informasi Lokasi -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Informasi Lokasi</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Nama Lokasi</label>
                        <p class="text-lg font-medium text-gray-900">{{ $location->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Deskripsi</label>
                        <p class="text-gray-900">{{ $location->description ?: 'Tidak ada deskripsi' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Jumlah Barang</label>
                        <p class="text-lg font-medium text-blue-600">{{ $location->items->count() }} barang</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Dibuat</label>
                        <p class="text-gray-900">{{ $location->created_at->format('d M Y H:i') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Terakhir Diupdate</label>
                        <p class="text-gray-900">{{ $location->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
                
                <div class="mt-6 flex space-x-3">
                    <a href="{{ route('locations.edit', $location) }}" 
                       class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Edit Lokasi
                    </a>
                </div>
            </div>
        </div>

        <!-- Daftar Barang -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Barang di Lokasi Ini</h2>
                </div>
                
                @if($location->items->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Barang
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kode
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kategori
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($location->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $item->code }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $item->category->name ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'Tersedia' => 'bg-green-100 text-green-800',
                                                    'Dipinjam' => 'bg-blue-100 text-blue-800',
                                                    'Dalam Perbaikan' => 'bg-yellow-100 text-yellow-800',
                                                    'Rusak' => 'bg-red-100 text-red-800',
                                                    'Hilang' => 'bg-gray-100 text-gray-800'
                                                ];
                                                $color = $statusColors[$item->status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('items.show', $item) }}" class="text-blue-600 hover:text-blue-900">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-8 text-center text-gray-500">
                        <p>Tidak ada barang yang ditempatkan di lokasi ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 