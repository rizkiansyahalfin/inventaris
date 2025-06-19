@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto mt-10 bg-white dark:bg-gray-800 p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-900 dark:text-white">Buat Permintaan Barang</h2>
    <form method="POST" action="{{ route('item_requests.store') }}">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nama Barang</label>
            <input id="name" name="name" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="{{ old('name') }}" required>
            @if($errors->has('name'))
                <div class="mt-2 text-sm text-red-600">{{ $errors->first('name') }}</div>
            @endif
        </div>
        <div class="mb-4">
            <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Deskripsi</label>
            <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
            @if($errors->has('description'))
                <div class="mt-2 text-sm text-red-600">{{ $errors->first('description') }}</div>
            @endif
        </div>
        <div class="mb-4">
            <label for="category_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Kategori</label>
            <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                <option value="">Pilih Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            @if($errors->has('category_id'))
                <div class="mt-2 text-sm text-red-600">{{ $errors->first('category_id') }}</div>
            @endif
        </div>
        <div class="mb-4">
            <label for="quantity" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Jumlah</label>
            <input id="quantity" name="quantity" type="number" min="1" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="{{ old('quantity', 1) }}" required>
            @if($errors->has('quantity'))
                <div class="mt-2 text-sm text-red-600">{{ $errors->first('quantity') }}</div>
            @endif
        </div>
        <div class="mb-6">
            <label for="reason" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Alasan Permintaan</label>
            <textarea id="reason" name="reason" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('reason') }}</textarea>
            @if($errors->has('reason'))
                <div class="mt-2 text-sm text-red-600">{{ $errors->first('reason') }}</div>
            @endif
        </div>
        <button type="submit" class="w-full justify-center py-3 px-4 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition">Buat Permintaan</button>
    </form>
</div>
@endsection 