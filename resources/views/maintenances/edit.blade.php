@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Data Pemeliharaan</h1>

    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900">{{ $maintenance->title }}</h2>
            <p class="text-sm text-gray-500">Tipe: {{ $maintenance->type }}</p>
            <p class="text-sm text-gray-500">Barang: {{ $maintenance->item->name }} ({{ $maintenance->item->code }})</p>
        </div>

        <form action="{{ route('maintenances.update', $maintenance) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                    <input type="date" id="start_date" value="{{ $maintenance->start_date->format('Y-m-d') }}" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">Tanggal mulai tidak dapat diubah</p>
                </div>

                <div>
                    <label for="completion_date" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                    <input type="date" id="completion_date" name="completion_date" value="{{ old('completion_date', $maintenance->completion_date ? $maintenance->completion_date->format('Y-m-d') : '') }}" min="{{ $maintenance->start_date->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('completion_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="cost" class="block text-sm font-medium text-gray-700">Biaya</label>
                <input type="number" id="cost" value="{{ $maintenance->cost }}" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">Biaya tidak dapat diubah setelah dibuat</p>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes', $maintenance->notes) }}</textarea>
                @error('notes')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @if(!$maintenance->completion_date)
            <div>
                <label for="update_condition" class="block text-sm font-medium text-gray-700">Update Kondisi Barang (Jika Menyelesaikan)</label>
                <select id="update_condition" name="update_condition" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Pilih kondisi setelah pemeliharaan</option>
                    <option value="Baik" @selected(old('update_condition') == 'Baik')>Baik</option>
                    <option value="Rusak Ringan" @selected(old('update_condition') == 'Rusak Ringan')>Rusak Ringan</option>
                    <option value="Rusak Berat" @selected(old('update_condition') == 'Rusak Berat')>Rusak Berat</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">Pilih kondisi barang setelah pemeliharaan selesai</p>
                @error('update_condition')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            @endif

            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Informasi</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Status saat ini: <span class="font-medium">{{ $maintenance->item->status }}</span></li>
                                <li>Kondisi saat ini: <span class="font-medium">{{ $maintenance->item->condition }}</span></li>
                                @if($maintenance->completion_date)
                                    <li>Pemeliharaan telah selesai pada {{ $maintenance->completion_date->format('d/m/Y') }}</li>
                                @else
                                    <li>Pemeliharaan masih berlangsung</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('maintenances.show', $maintenance) }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300">Batal</a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Update</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const completionDateInput = document.getElementById('completion_date');
        const updateConditionSelect = document.getElementById('update_condition');
        
        if (completionDateInput && updateConditionSelect) {
            completionDateInput.addEventListener('change', function() {
                if (this.value) {
                    // Show update condition field when completion date is set
                    updateConditionSelect.parentElement.style.display = 'block';
                } else {
                    // Hide update condition field when completion date is cleared
                    updateConditionSelect.parentElement.style.display = 'none';
                    updateConditionSelect.value = '';
                }
            });
            
            // Initial state
            if (completionDateInput.value) {
                updateConditionSelect.parentElement.style.display = 'block';
            } else {
                updateConditionSelect.parentElement.style.display = 'none';
            }
        }
    });
</script>
@endpush
@endsection 