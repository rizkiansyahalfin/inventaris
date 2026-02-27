@extends('layouts.app')

@section('content')
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Edit Barang</h2>
                <div class="flex space-x-3">
                    <a href="{{ route('items.show', $item) }}"
                        class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                        Kembali ke Detail
                    </a>
                </div>
            </div>

            <form action="{{ route('items.update', $item) }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                            Barang</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $item->name) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-500 @enderror"
                            required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode
                            Barang</label>
                        <p class="mt-1 text-gray-900 dark:text-gray-200">{{ $item->code }}</p>
                    </div>

                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah
                            Barang</label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', $item->stock) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('stock') border-red-500 @enderror"
                            required min="1">
                        @error('stock')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Saat Ini</label>
                        <p class="mt-1 text-gray-900 dark:text-gray-200">
                            @if($item->status == 'Tersedia')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Tersedia</span>
                            @elseif($item->status == 'Dipinjam')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Dipinjam</span>
                            @elseif($item->status == 'Dalam Perbaikan')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">Dalam
                                    Perbaikan</span>
                            @elseif($item->status == 'Rusak')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">Rusak</span>
                            @elseif($item->status == 'Hilang')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Hilang</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label for="condition" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kondisi
                            Fisik</label>
                        <select name="condition" id="condition"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('condition') border-red-500 @enderror"
                            required>
                            <option value="">Pilih Kondisi</option>
                            <option value="Baik" {{ old('condition', $item->condition) == 'Baik' ? 'selected' : '' }}>Baik
                            </option>
                            <option value="Rusak Ringan" {{ old('condition', $item->condition) == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="Rusak Berat" {{ old('condition', $item->condition) == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                        @error('condition')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="location_id"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi</label>
                        <select name="location_id" id="location_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('location_id') border-red-500 @enderror">
                            <option value="">Pilih Lokasi</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ old('location_id', $item->location_id) == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('location_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="purchase_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga
                            Beli</label>
                        <input type="number" name="purchase_price" id="purchase_price"
                            value="{{ old('purchase_price', $item->purchase_price) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('purchase_price') border-red-500 @enderror"
                            min="0" step="0.01">
                        @error('purchase_price')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="purchase_date"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Pembelian</label>
                        <input type="date" name="purchase_date" id="purchase_date"
                            value="{{ old('purchase_date', $item->purchase_date->format('Y-m-d')) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('purchase_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select name="status" id="status"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="Tersedia" @selected(old('status', $item->status) == 'Tersedia')>Tersedia</option>
                            <option value="Dipinjam" @selected(old('status', $item->status) == 'Dipinjam')>Dipinjam</option>
                            <option value="Dalam Perbaikan" @selected(old('status', $item->status) == 'Dalam Perbaikan')>Dalam
                                Perbaikan</option>
                            <option value="Rusak" @selected(old('status', $item->status) == 'Rusak')>Rusak</option>
                            <option value="Hilang" @selected(old('status', $item->status) == 'Hilang')>Hilang</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div x-data="{ showModal: false, imageUrl: '' }">
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gambar
                            Barang</label>
                        <input type="file" name="image" id="image"
                            class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 dark:text-gray-300 focus:outline-none @error('image') border-red-500 @enderror">
                        @error('image')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        @if ($item->image)
                            <div class="mt-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Gambar saat ini:</p>
                                <div class="mt-2 p-2 border dark:border-gray-600 rounded-lg inline-block">
                                    <img @click="showModal = true; imageUrl = '{{ asset('storage/items/' . $item->image) }}'"
                                        src="{{ asset('storage/items/' . $item->image) }}" alt="{{ $item->name }}"
                                        class="h-40 w-auto object-cover rounded-md cursor-pointer hover:opacity-90 transition-opacity">
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Modal -->
                    <div x-show="showModal" @keydown.escape.window="showModal = false"
                        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75"
                        style="display: none;">
                        <div @click.away="showModal = false"
                            class="relative bg-white dark:bg-gray-800 rounded-lg max-w-4xl max-h-full overflow-auto">
                            <button type="button" @click="showModal = false"
                                class="absolute top-2 right-2 text-gray-300 dark:text-gray-500 hover:text-white dark:hover:text-gray-300 focus:outline-none z-10">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <img :src="imageUrl" class="w-full h-auto">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="description"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                    <textarea name="description" id="description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') border-red-500 @enderror">{{ old('description', $item->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</label>
                    <select name="category_id" id="category_id"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('category_id') border-red-500 @enderror"
                        required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection