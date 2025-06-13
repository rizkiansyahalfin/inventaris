@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Tambah Stok Barang</h2>
                    <p class="text-sm text-gray-500">{{ $item->name }} (Kode: {{ $item->code }})</p>
                </div>
                <div>
                    <a href="{{ route('items.show', $item) }}" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200">
                        Kembali
                    </a>
                </div>
            </div>

            <form action="{{ route('items.add-stock', $item) }}" method="POST" class="mt-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Jumlah yang ditambahkan -->
                    <div>
                        <label for="quantity_to_add" class="block text-sm font-medium leading-6 text-gray-900">Jumlah yang Ditambahkan <span class="text-red-500">*</span></label>
                        <div class="mt-2">
                            <input type="number" min="1" id="quantity_to_add" name="quantity_to_add" value="{{ old('quantity_to_add', 1) }}" required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        @error('quantity_to_add')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kondisi -->
                    <div>
                        <label for="condition" class="block text-sm font-medium leading-6 text-gray-900">Kondisi <span class="text-red-500">*</span></label>
                        <div class="mt-2">
                            <select id="condition" name="condition" required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option value="Baik" {{ old('condition') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                <option value="Rusak Ringan" {{ old('condition') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                <option value="Rusak Berat" {{ old('condition') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                            </select>
                        </div>
                        @error('condition')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Pembelian -->
                    <div>
                        <label for="purchase_date" class="block text-sm font-medium leading-6 text-gray-900">Tanggal Pembelian Baru</label>
                        <div class="mt-2">
                            <input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Opsional, jika stok berasal dari pembelian baru</p>
                        @error('purchase_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga Pembelian -->
                    <div>
                        <label for="purchase_price" class="block text-sm font-medium leading-6 text-gray-900">Harga Pembelian Baru</label>
                        <div class="mt-2">
                            <input type="number" min="0" step="1000" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Opsional, jika stok berasal dari pembelian baru</p>
                        @error('purchase_price')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Catatan -->
                <div>
                    <label for="notes" class="block text-sm font-medium leading-6 text-gray-900">Catatan</label>
                    <div class="mt-2">
                        <textarea id="notes" name="notes" rows="3"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ old('notes') }}</textarea>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Informasi tambahan tentang penambahan stok ini</p>
                    @error('notes')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status saat ini -->
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-md font-medium text-gray-900">Informasi Stok Saat Ini</h3>
                    <dl class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jumlah Saat Ini</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $item->quantity }} unit</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kondisi Saat Ini</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $item->condition }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status Saat Ini</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $item->status }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('items.show', $item) }}" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Tambah Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 