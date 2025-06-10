@extends('layouts.app')

@section('content')
<div class="bg-white overflow-hidden shadow-sm rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Edit Barang</h2>
            <div class="flex space-x-3">
                <a href="{{ route('items.show', $item) }}" class="text-gray-600 hover:text-gray-900">
                    Kembali ke Detail
                </a>
            </div>
        </div>

        <form action="{{ route('items.update', $item) }}" method="POST" class="space-y-6" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $item->name) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-500 @enderror"
                        required>
                    @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Kode Barang</label>
                    <p class="mt-1 text-gray-900">{{ $item->code }}</p>
                </div>

                <div>
                    <label for="condition" class="block text-sm font-medium text-gray-700">Kondisi</label>
                    <select name="condition" id="condition"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('condition') border-red-500 @enderror"
                        required>
                        <option value="">Pilih Kondisi</option>
                        <option value="Baik" {{ old('condition', $item->condition) == 'Baik' ? 'selected' : '' }}>Baik</option>
                        <option value="Rusak Ringan" {{ old('condition', $item->condition) == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="Rusak Berat" {{ old('condition', $item->condition) == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                    </select>
                    @error('condition')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Lokasi</label>
                    <input type="text" name="location" id="location" value="{{ old('location', $item->location) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('location') border-red-500 @enderror">
                    @error('location')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="purchase_price" class="block text-sm font-medium text-gray-700">Harga Beli</label>
                    <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $item->purchase_price) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('purchase_price') border-red-500 @enderror"
                        min="0" step="0.01">
                    @error('purchase_price')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="purchase_date" class="block text-sm font-medium text-gray-700">Tanggal Pembelian</label>
                    <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', optional($item->purchase_date)->format('Y-m-d')) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('purchase_date') border-red-500 @enderror">
                    @error('purchase_date')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="image" class="block text-sm font-medium text-gray-700">Gambar Barang</label>
                <input type="file" name="image" id="image"
                    class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none @error('image') border-red-500 @enderror">
                @error('image')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                @if ($item->image)
                    <div class="mt-4">
                        <p class="text-sm text-gray-600">Gambar saat ini:</p>
                        <img src="{{ asset('storage/items/' . $item->image) }}" alt="{{ $item->name }}" class="mt-2 h-32 w-auto object-cover rounded-md">
                    </div>
                @endif
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') border-red-500 @enderror">{{ old('description', $item->description) }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Kategori</label>
                <div class="mt-2 space-y-2">
                    @foreach($categories as $category)
                    <div class="flex items-center">
                        <input type="checkbox" name="category_ids[]" id="category_{{ $category->id }}"
                            value="{{ $category->id }}" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            {{ in_array($category->id, old('category_ids', $item->categories->pluck('id')->toArray())) ? 'checked' : '' }}>
                        <label for="category_{{ $category->id }}" class="ml-2 text-sm text-gray-700">
                            {{ $category->name }}
                        </label>
                    </div>
                    @endforeach
                </div>
                @error('category_ids')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 