@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto mt-10 bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-900">Detail Permintaan Barang</h2>
    <div class="mb-4">
        <span class="font-semibold">Nama Barang:</span>
        <span>{{ $itemRequest->name }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold">Deskripsi:</span>
        <span>{{ $itemRequest->description }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold">Kategori:</span>
        <span>{{ optional($itemRequest->category)->name ?? '-' }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold">Jumlah:</span>
        <span>{{ $itemRequest->quantity }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold">Alasan Permintaan:</span>
        <span>{{ $itemRequest->reason }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold">Status:</span>
        <span>
            @if($itemRequest->status === 'pending')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menunggu</span>
            @elseif($itemRequest->status === 'approved')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>
            @elseif($itemRequest->status === 'rejected')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
            @elseif($itemRequest->status === 'completed')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Selesai</span>
            @endif
        </span>
    </div>
    <div class="mb-4">
        <span class="font-semibold">Pemohon:</span>
        <span>{{ optional($itemRequest->user)->name ?? '-' }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold">Reviewer:</span>
        <span>{{ optional($itemRequest->reviewer)->name ?? '-' }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold">Catatan Review:</span>
        <span>{{ $itemRequest->review_notes ?? '-' }}</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold">Tanggal Permintaan:</span>
        <span>{{ $itemRequest->created_at->format('d/m/Y H:i') }}</span>
    </div>
    <div class="flex justify-end mt-8 gap-2">
        @if($itemRequest->status === 'pending' && auth()->id() === $itemRequest->user_id)
            <a href="{{ route('item-requests.edit', $itemRequest) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Edit</a>
        @endif
        <a href="{{ route('item-requests.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Kembali</a>
    </div>
    @if((auth()->user()->isAdmin() || auth()->user()->isPetugas()) && in_array($itemRequest->status, ['pending', 'approved']))
        <form method="POST" action="{{ route('item-requests.update_status', $itemRequest) }}" class="mt-8 bg-gray-50 p-4 rounded-lg border">
            @csrf
            <div class="mb-3">
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Ubah Status Permintaan</label>
                <select id="status" name="status" class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="approved" {{ $itemRequest->status === 'approved' ? 'selected' : '' }}>Setujui</option>
                    <option value="rejected" {{ $itemRequest->status === 'rejected' ? 'selected' : '' }}>Tolak</option>
                    <option value="completed" {{ $itemRequest->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="review_notes" class="block text-sm font-semibold text-gray-700 mb-1">Catatan Review (opsional)</label>
                <textarea id="review_notes" name="review_notes" class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('review_notes', $itemRequest->review_notes) }}</textarea>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded">Simpan Status</button>
        </form>
    @endif
</div>
@endsection 