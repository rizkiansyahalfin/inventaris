@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Informasi Barang -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $item->name }}</h2>
                    <p class="text-sm text-gray-500">Kode: {{ $item->code }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('items.add-stock.form', $item) }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        Tambah Stok
                    </a>
                    <a href="{{ route('items.edit', $item) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Edit
                    </a>
                    <a href="{{ route('items.index') }}" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200">
                        Kembali
                    </a>
                </div>
            </div>

            <div x-data="{ showModal: false, imageUrl: '' }" class="mt-6 flex justify-center">
                <div class="w-full max-w-2xl p-4 border rounded-lg shadow-inner bg-gray-50">
                    @if($item->image)
                        <img @click="showModal = true; imageUrl = '{{ asset('storage/items/' . $item->image) }}'" src="{{ asset('storage/items/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-auto max-h-[500px] object-contain rounded-md cursor-pointer hover:opacity-90 transition-opacity">
                    @else
                        <div class="w-full h-80 bg-gray-200 flex items-center justify-center rounded-lg">
                            <p class="text-gray-500">Tidak ada gambar</p>
                        </div>
                    @endif
                </div>

                <!-- Modal -->
                <div x-show="showModal" @keydown.escape.window="showModal = false" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75" style="display: none;">
                    <div @click.away="showModal = false" class="relative bg-white rounded-lg max-w-4xl max-h-full overflow-auto">
                        <button @click="showModal = false" class="absolute top-2 right-2 text-gray-300 hover:text-white focus:outline-none z-10">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                        <img :src="imageUrl" class="w-full h-auto">
                    </div>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Informasi Umum</h3>
                    <dl class="mt-2 space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jumlah Peminjaman</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $item->borrows_count }} kali</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">QR Code</dt>
                            <dd class="mt-1">
                                <div class="qrcode-container">
                                    {!! QrCode::size(120)->generate($item->code) !!}
                                </div>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kondisi</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $item->condition }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Lokasi</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $item->location ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                            <dd class="mt-1">
                                @foreach($item->categories as $category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-1">
                                    {{ $category->name }}
                                </span>
                                @endforeach
                            </dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900">Informasi Pembelian</h3>
                    <dl class="mt-2 space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Harga Beli</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $item->purchase_price ? 'Rp ' . number_format($item->purchase_price, 0, ',', '.') : '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Pembelian</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $item->purchase_date ? $item->purchase_date->format('d/m/Y') : '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if($item->description)
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900">Deskripsi</h3>
                <div class="mt-2 text-sm text-gray-600">
                    {{ $item->description }}
                </div>
            </div>
            @endif

            @if($item->notes)
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900">Catatan Stok</h3>
                <div class="mt-2 p-4 bg-gray-50 rounded-md">
                    <pre class="text-sm text-gray-600 whitespace-pre-wrap">{{ $item->notes }}</pre>
                </div>
            </div>
            @endif

            @if($item->stock > 1)
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900">Daftar Unit Barang</h3>
                <div class="mt-2 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Kode Unit</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Status</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Kondisi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($relatedItems as $unit)
                            <tr>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 font-medium">{{ $unit->code }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @if($unit->status == 'Tersedia')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Tersedia</span>
                                    @elseif($unit->status == 'Dipinjam')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Dipinjam</span>
                                    @elseif($unit->status == 'Dalam Perbaikan')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Dalam Perbaikan</span>
                                    @elseif($unit->status == 'Rusak')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Rusak</span>
                                    @elseif($unit->status == 'Hilang')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Hilang</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $unit->condition }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Lampiran -->
    @if($item->attachments->isNotEmpty())
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900">Lampiran</h3>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($item->attachments as $attachment)
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

    <!-- Riwayat Peminjaman -->
    @if($item->borrows->isNotEmpty())
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900">Riwayat Peminjaman</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                Peminjam</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                Tgl Pinjam</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                Kondisi Pinjam</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                Tgl Kembali</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                Kondisi Kembali</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                Status</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                <span class="sr-only">Detail</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($item->borrows as $borrow)
                        <tr>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $borrow->user->name }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $borrow->borrow_date->format('d/m/Y') }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $borrow->condition_at_borrow }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $borrow->return_date ? $borrow->return_date->format('d/m/Y') : '-' }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $borrow->condition_on_return ?? '-' }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $borrow->status === 'borrowed' ? 'bg-yellow-50 text-yellow-800 ring-1 ring-inset ring-yellow-600/20' : ($borrow->status === 'returned' ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20' : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10') }}">
                                    {{ ucfirst($borrow->status) }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                <a href="{{ route('borrows.show', $borrow) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection 