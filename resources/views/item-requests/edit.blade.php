@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 sm:p-8">
                <!-- Page Header -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Permintaan Barang</h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Perbarui informasi permintaan barang yang telah diajukan</p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('item-requests.update', $itemRequest) }}" class="space-y-6">
                    @csrf
                    @method('PATCH')
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nama Barang <span class="text-red-500">*</span>
                        </label>
                        <input id="name" name="name" type="text" 
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400" 
                               value="{{ old('name', $itemRequest->name) }}" 
                               placeholder="Masukkan nama barang yang diminta"
                               required>
                        @if($errors->has('name'))
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $errors->first('name') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Deskripsi <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                                  placeholder="Jelaskan detail barang yang diminta">{{ old('description', $itemRequest->description) }}</textarea>
                        @if($errors->has('description'))
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $errors->first('description') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Kategori
                        </label>
                        <select id="category_id" name="category_id" 
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400">
                            <option value="">Pilih Kategori (Opsional)</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $itemRequest->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('category_id'))
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $errors->first('category_id') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Jumlah <span class="text-red-500">*</span>
                        </label>
                        <input id="quantity" name="quantity" type="number" min="1" 
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400" 
                               value="{{ old('quantity', $itemRequest->quantity) }}" 
                               placeholder="Masukkan jumlah yang dibutuhkan"
                               required>
                        @if($errors->has('quantity'))
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $errors->first('quantity') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Alasan Permintaan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="reason" name="reason" rows="4"
                                  class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                                  placeholder="Jelaskan alasan mengapa barang ini diperlukan">{{ old('reason', $itemRequest->reason) }}</textarea>
                        @if($errors->has('reason'))
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $errors->first('reason') }}</p>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('item-requests.show', $itemRequest) }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Batal
                        </a>
                        
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 