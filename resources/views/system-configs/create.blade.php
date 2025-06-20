@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Tambah Konfigurasi Sistem</h2>
                    <a href="{{ route('system-configs.index') }}" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <form action="{{ route('system-configs.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="key" class="block text-sm font-medium text-gray-700">Kunci Konfigurasi</label>
                        <input type="text" name="key" id="key" value="{{ old('key') }}" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required>
                        @error('key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="value" class="block text-sm font-medium text-gray-700">Nilai Konfigurasi</label>
                        <textarea name="value" id="value" rows="3" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required>{{ old('value') }}</textarea>
                        @error('value')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 