@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $maintenance->title }}</h2>
                    <p class="text-sm text-gray-500">Tipe: {{ $maintenance->type }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('maintenances.index') }}" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200">
                        Kembali ke Daftar
                    </a>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Informasi Barang</h3>
                    <dl class="mt-2 space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama Barang</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="{{ route('items.show', $maintenance->item) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $maintenance->item->name }}
                                </a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kode Barang</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->item->code }}</dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900">Detail Pemeliharaan</h3>
                    <dl class="mt-2 space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Mulai</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->start_date->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Selesai</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->completion_date ? $maintenance->completion_date->format('d/m/Y') : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Biaya</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->cost ? 'Rp ' . number_format($maintenance->cost, 0, ',', '.') : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ditangani oleh</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenance->user->name ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if($maintenance->notes)
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900">Catatan</h3>
                <div class="mt-2 text-sm text-gray-600 whitespace-pre-wrap">
                    {{ $maintenance->notes }}
                </div>
            </div>
            @endif
            
            <div class="mt-6 border-t pt-4 flex justify-end space-x-4">
                <a href="{{ route('maintenances.edit', $maintenance) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Edit
                </a>
                <form action="{{ route('maintenances.destroy', $maintenance) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus data pemeliharaan ini?')">
                        Hapus Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 