@extends('layouts.app')

@section('content')
<div class="bg-white overflow-hidden shadow-sm rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Ajukan Peminjaman Baru</h2>
            <a href="{{ route('borrows.index') }}" class="text-gray-600 hover:text-gray-900">
                Kembali ke Daftar
            </a>
        </div>

        <!-- Info Box -->
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Informasi Pengajuan</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Pengajuan peminjaman Anda akan ditinjau oleh petugas atau admin. Anda akan menerima notifikasi setelah pengajuan disetujui atau ditolak.</p>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('borrows.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="item_id" class="block text-sm font-medium text-gray-700">Barang</label>
                    <select name="item_id" id="item_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('item_id') border-red-500 @enderror"
                        required>
                        <option value="">Pilih Barang</option>
                        @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->name }} ({{ $item->code }}) - Stok: {{ $item->stock }}
                        </option>
                        @endforeach
                    </select>
                    @error('item_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="borrow_date" class="block text-sm font-medium text-gray-700">Tanggal Pinjam</label>
                    <input type="date" name="borrow_date" id="borrow_date" value="{{ old('borrow_date', date('Y-m-d')) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('borrow_date') border-red-500 @enderror"
                        required>
                    @error('borrow_date')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700">Tanggal Jatuh Tempo</label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('due_date') border-red-500 @enderror"
                        required>
                    @error('due_date')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                <textarea name="notes" id="notes" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('notes') border-red-500 @enderror"
                    placeholder="Tambahkan catatan atau alasan peminjaman...">{{ old('notes') }}</textarea>
                @error('notes')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Ajukan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('borrow_date').addEventListener('change', function() {
    const borrowDate = new Date(this.value);
    const dueDate = document.getElementById('due_date');
    
    if (dueDate.value) {
        const dueDateValue = new Date(dueDate.value);
        if (dueDateValue <= borrowDate) {
            dueDate.value = '';
        }
    }
    
    dueDate.min = this.value;
});
</script>
@endpush

@endsection 