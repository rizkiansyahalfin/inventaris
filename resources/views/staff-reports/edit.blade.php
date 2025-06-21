@extends('layouts.app')

@section('content')
<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Laporan Staff') }}
        </h2>
        <a href="{{ route('staff-reports.show', $staffReport) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800">
            {{ __('Kembali') }}
        </a>
    </div>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <form action="{{ route('staff-reports.update', $staffReport) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tanggal Laporan -->
                        <div>
                            <label for="report_date" class="block text-sm font-medium text-gray-700">Tanggal Laporan *</label>
                            <input type="date" name="report_date" id="report_date" 
                                   value="{{ old('report_date', $staffReport->report_date->format('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                   required>
                            @error('report_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jam Kerja -->
                        <div>
                            <label for="hours_worked" class="block text-sm font-medium text-gray-700">Jam Kerja *</label>
                            <input type="number" name="hours_worked" id="hours_worked" 
                                   value="{{ old('hours_worked', $staffReport->hours_worked) }}"
                                   step="0.5" min="0.5" max="24"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                   placeholder="Contoh: 8.5"
                                   required>
                            @error('hours_worked')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Aktivitas -->
                    <div class="mt-6">
                        <label for="activities" class="block text-sm font-medium text-gray-700">Aktivitas yang Dilakukan *</label>
                        <textarea name="activities" id="activities" rows="6"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                  placeholder="Jelaskan aktivitas yang Anda lakukan hari ini..."
                                  required>{{ old('activities', $staffReport->activities) }}</textarea>
                        @error('activities')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tantangan -->
                    <div class="mt-6">
                        <label for="challenges" class="block text-sm font-medium text-gray-700">Tantangan/Hambatan</label>
                        <textarea name="challenges" id="challenges" rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                  placeholder="Jelaskan tantangan atau hambatan yang Anda hadapi (opsional)...">{{ old('challenges', $staffReport->challenges) }}</textarea>
                        @error('challenges')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mt-6">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                        <select name="status" id="status" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                required>
                            <option value="draft" {{ old('status', $staffReport->status) == 'draft' ? 'selected' : '' }}>Draft (Simpan sementara)</option>
                            <option value="submitted" {{ old('status', $staffReport->status) == 'submitted' ? 'selected' : '' }}>Submit (Kirim ke admin)</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tombol Submit -->
                    <div class="mt-8 flex justify-end space-x-3">
                        <a href="{{ route('staff-reports.show', $staffReport) }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800">
                            Batal
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800">
                            Update Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 