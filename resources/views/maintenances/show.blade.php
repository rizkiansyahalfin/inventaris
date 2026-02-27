@extends('layouts.app')

@section('content')
    @php /** @var \App\Models\Maintenance $maintenance */ @endphp
    <div class="space-y-6">
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border dark:border-gray-700 transition-colors">
            <div class="p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $maintenance->title }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tipe: {{ $maintenance->type }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('maintenances.index') }}"
                            class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Kembali ke Daftar
                        </a>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Barang</h3>
                        <dl class="mt-2 space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Barang</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">
                                    <a href="{{ route('items.show', $maintenance->item) }}"
                                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors">
                                        {{ $maintenance->item->name }}
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kode Barang</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">{{ $maintenance->item->code }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Detail Pemeliharaan</h3>
                        <dl class="mt-2 space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Mulai</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                    {{ \Illuminate\Support\Carbon::parse($maintenance->start_date)->format('d/m/Y') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Selesai</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                    {{ $maintenance->completion_date ? \Illuminate\Support\Carbon::parse($maintenance->completion_date)->format('d/m/Y') : '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Biaya</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                    {{ $maintenance->cost ? 'Rp ' . number_format((float) $maintenance->cost, 0, ',', '.') : '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Ditangani oleh</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                    {{ $maintenance->user->name ?? 'N/A' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                @if($maintenance->notes)
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Catatan</h3>
                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap">
                            {{ $maintenance->notes }}
                        </div>
                    </div>
                @endif

                <div class="mt-6 border-t dark:border-gray-700 pt-4 flex justify-end space-x-4">
                    <a href="{{ route('maintenances.edit', $maintenance) }}"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors">
                        Edit
                    </a>
                    <form action="{{ route('maintenances.destroy', $maintenance) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus data pemeliharaan ini?')">
                            Hapus Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection