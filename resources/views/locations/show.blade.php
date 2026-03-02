@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border dark:border-gray-700 transition-colors">
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Detail Lokasi</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Kode: {{ $location->code }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('locations.index') }}"
                            class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Kembali
                        </a>
                        <a href="{{ route('locations.edit', $location) }}"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors">
                            Edit Lokasi
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border dark:border-gray-700">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Informasi Gedung</h3>
                        <dl class="space-y-3">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Kode</h3>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $location->code }}</p>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Gedung</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ $location->building ?? '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Lantai</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ $location->floor ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Lokasi</h3>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $location->name }}</p>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Deskripsi</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            {{ $location->description ?? 'Tidak ada deskripsi.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Barang di Lokasi Ini -->
        <div
            class="bg-white dark:bg-gray-900 rounded-lg shadow overflow-hidden border dark:border-gray-700">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">Daftar Barang di Lokasi Ini</h3>
                @forelse($location->items as $item)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $item->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->category?->name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $item->status === 'Tersedia' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                {{ $item->status === 'Dipinjam' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                {{ in_array($item->status, ['Rusak', 'Hilang', 'Dalam Perbaikan']) ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('items.show', $item) }}"
                                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">Belum ada barang di lokasi ini.</p>
                @endif
            </div>
        </div>
    </div>
@endsection