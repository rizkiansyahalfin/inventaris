@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6 transition-colors">Tambah Data Pemeliharaan</h1>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 border dark:border-gray-700 transition-colors">
            <form action="{{ route('maintenances.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="item_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Barang</label>
                    <select id="item_id" name="item_id"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required>
                        <option value="">Pilih barang</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" @selected(old('item_id', $selectedItem) == $item->id)>
                                {{ $item->name }} ({{ $item->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('item_id')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe
                        Pemeliharaan</label>
                    <select id="type" name="type"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required>
                        <option value="Perawatan" @selected(old('type') == 'Perawatan')>Perawatan</option>
                        <option value="Perbaikan" @selected(old('type') == 'Perbaikan')>Perbaikan</option>
                        <option value="Penggantian" @selected(old('type') == 'Penggantian')>Penggantian</option>
                    </select>
                    @error('type')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('title')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                        Mulai</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('start_date')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="completion_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                        Selesai (Opsional)</label>
                    <input type="date" id="completion_date" name="completion_date" value="{{ old('completion_date') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('completion_date')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Biaya
                        (Opsional)</label>
                    <input type="number" id="cost" name="cost" value="{{ old('cost') }}" step="0.01"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('cost')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan
                        (Opsional)</label>
                    <textarea id="notes" name="notes" rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="update_item_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ubah
                        Status Barang (Opsional)</label>
                    <select id="update_item_status" name="update_item_status"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Jangan ubah status</option>
                        <option value="Tersedia" @selected(old('update_item_status') == 'Tersedia')>Tersedia</option>
                        <option value="Perlu Servis" @selected(old('update_item_status') == 'Perlu Servis')>Perlu Servis
                        </option>
                        <option value="Rusak" @selected(old('update_item_status') == 'Rusak')>Rusak</option>
                        <option value="Perlu Ganti" @selected(old('update_item_status') == 'Perlu Ganti')>Perlu Ganti</option>
                    </select>
                    @error('update_item_status')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ url()->previous() }}"
                        class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">Batal</a>
                    <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const itemsData = @json($itemsWithCondition);
                const itemSelect = document.getElementById('item_id');
                const typeSelect = document.getElementById('type');
                const perbaikanOption = typeSelect.querySelector('option[value="Perbaikan"]');

                function togglePerbaikanOption() {
                    const selectedItemId = itemSelect.value;
                    if (!selectedItemId || !itemsData[selectedItemId]) {
                        // Show all options if no item is selected
                        perbaikanOption.style.display = 'block';
                        return;
                    }

                    const condition = itemsData[selectedItemId].condition;

                    if (condition === 'Baik') {
                        // If selected type was 'Perbaikan', reset it
                        if (typeSelect.value === 'Perbaikan') {
                            typeSelect.value = 'Perawatan';
                        }
                        perbaikanOption.style.display = 'none';
                    } else {
                        perbaikanOption.style.display = 'block';
                    }
                }

                itemSelect.addEventListener('change', togglePerbaikanOption);

                // Initial check on page load
                togglePerbaikanOption();
            });
        </script>
    @endpush
@endsection