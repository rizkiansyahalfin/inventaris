@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Informasi Peminjaman -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Detail Peminjaman</h2>
                    <p class="text-sm text-gray-500">Status:
                        @if($borrow->status === 'borrowed')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Dipinjam
                        </span>
                        @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Dikembalikan
                        </span>
                        @endif
                    </p>
                </div>
                <div class="flex space-x-3">
                    @if($borrow->status === 'borrowed')
                    <form action="{{ route('borrows.return', $borrow) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700"
                            onclick="return confirm('Apakah Anda yakin ingin mengembalikan barang ini?')">
                            Kembalikan
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('borrows.index') }}" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200">
                        Kembali
                    </a>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Informasi Barang</h3>
                    <dl class="mt-2 space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama Barang</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $borrow->item->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kode Barang</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $borrow->item->code }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jumlah Dipinjam</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $borrow->quantity }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kondisi Barang</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $borrow->item->condition }}</dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900">Informasi Peminjaman</h3>
                    <dl class="mt-2 space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Peminjam</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $borrow->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Pinjam</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $borrow->borrow_date->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jatuh Tempo</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="{{ $borrow->due_date < now() && $borrow->status === 'borrowed' ? 'text-red-600 font-medium' : '' }}">
                                    {{ $borrow->due_date->format('d/m/Y') }}
                                </span>
                            </dd>
                        </div>
                        @if($borrow->return_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Kembali</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $borrow->return_date->format('d/m/Y') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            @if($borrow->notes)
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900">Catatan</h3>
                <div class="mt-2 text-sm text-gray-600">
                    {{ $borrow->notes }}
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Lampiran -->
    @if($borrow->attachments->isNotEmpty())
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900">Lampiran</h3>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($borrow->attachments as $attachment)
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $attachment->file_name }}</p>
                                <p class="text-sm text-gray-500">{{ number_format($attachment->file_size / 1024, 2) }} KB</p>
                            </div>
                        </div>
                        <form action="{{ route('attachments.destroy', $attachment) }}" method="POST" class="flex">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus lampiran ini?')">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection 