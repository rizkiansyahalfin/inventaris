@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Informasi Barang -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $item->name }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Kode: {{ $item->code }}</p>
                    </div>
                    <div class="flex space-x-3 items-center">
                        @auth
                            @php
                                $isBookmarked = auth()->user()->bookmarks->contains('item_id', $item->id);
                            @endphp
                            <form action="{{ route('bookmarks.toggle', $item) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium focus:outline-none transition-colors
                                        {{ $isBookmarked ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:hover:bg-yellow-900/50' : 'bg-gray-200 text-gray-700 hover:bg-yellow-100 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-yellow-900/20' }}">
                                    @if($isBookmarked)
                                        <svg class="w-5 h-5 mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        Hapus dari Favorit
                                    @else
                                        <svg class="w-5 h-5 mr-1 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                        </svg>
                                        Tandai Favorit
                                    @endif
                                </button>
                            </form>
                        @endauth
                        <a href="{{ route('items.add-stock.form', $item) }}"
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 transition-colors">
                            Tambah Stok
                        </a>
                        <a href="{{ route('items.edit', $item) }}"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors">
                            Edit
                        </a>
                        <a href="{{ route('items.index') }}"
                            class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            Kembali
                        </a>
                    </div>
                </div>

                <div x-data="{ showModal: false, imageUrl: '' }" class="mt-6 flex justify-center">
                    <div
                        class="w-full max-w-2xl p-4 border dark:border-gray-700 rounded-lg shadow-inner bg-gray-50 dark:bg-gray-900">
                        @if($item->image)
                            <img @click="showModal = true; imageUrl = '{{ asset('storage/items/' . $item->image) }}'"
                                src="{{ asset('storage/items/' . $item->image) }}" alt="{{ $item->name }}"
                                class="w-full h-auto max-h-[500px] object-contain rounded-md cursor-pointer hover:opacity-90 transition-opacity">
                        @else
                            <div class="w-full h-80 bg-gray-200 dark:bg-gray-700 flex items-center justify-center rounded-lg">
                                <p class="text-gray-500 dark:text-gray-400">Tidak ada gambar</p>
                            </div>
                        @endif
                    </div>

                    <!-- Modal -->
                    <div x-show="showModal" @keydown.escape.window="showModal = false"
                        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75"
                        style="display: none;">
                        <div @click.away="showModal = false"
                            class="relative bg-white dark:bg-gray-800 rounded-lg max-w-4xl max-h-full overflow-auto">
                            <button @click="showModal = false"
                                class="absolute top-2 right-2 text-gray-300 dark:text-gray-500 hover:text-white dark:hover:text-gray-300 focus:outline-none z-10">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <img :src="imageUrl" class="w-full h-auto">
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Umum</h3>
                        <dl class="mt-2 space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Peminjaman</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">{{ $item->borrows_count }} kali
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">QR Code</dt>
                                <dd class="mt-1">
                                    <div class="qrcode-container p-2 bg-white rounded-lg inline-block">
                                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->generate($item->code) !!}
                                    </div>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kondisi</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">{{ $item->condition }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Lokasi</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                    @if($item->location)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $item->location->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">Tidak ada lokasi</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kategori</dt>
                                <dd class="mt-1">
                                    @if($item->category)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                            {{ $item->category->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">Tidak ada kategori</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Pembelian</h3>
                        <dl class="mt-2 space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Harga Beli</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                    {{ $item->purchase_price ? 'Rp ' . number_format($item->purchase_price, 0, ',', '.') : '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pembelian</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                    {{ $item->purchase_date ? $item->purchase_date->format('d/m/Y') : '-' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                @if($item->description)
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Deskripsi</h3>
                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ $item->description }}
                        </div>
                    </div>
                @endif

                @if($item->notes)
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Catatan Stok</h3>
                        <div class="mt-2 p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                            <pre class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-wrap">{{ $item->notes }}</pre>
                        </div>
                    </div>
                @endif

                @if($item->stock > 1)
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Daftar Unit Barang</h3>
                        <div class="mt-2 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">
                                            Kode Unit</th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">
                                            Status</th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">
                                            Kondisi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($relatedItems as $unit)
                                        <tr>
                                            <td
                                                class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                                {{ $unit->code }}</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                                @if($unit->status == 'Tersedia')
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Tersedia</span>
                                                @elseif($unit->status == 'Dipinjam')
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Dipinjam</span>
                                                @elseif($unit->status == 'Dalam Perbaikan')
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">Dalam
                                                        Perbaikan</span>
                                                @elseif($unit->status == 'Rusak')
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">Rusak</span>
                                                @elseif($unit->status == 'Hilang')
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Hilang</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                {{ $unit->condition }}</td>
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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Lampiran</h3>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($item->attachments as $attachment)
                            <div class="border dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $attachment->file_name }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ number_format($attachment->file_size / 1024, 2) }} KB</p>
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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Riwayat Peminjaman</h3>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">
                                        Peminjam</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">
                                        Tgl Pinjam</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">
                                        Kondisi Pinjam</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">
                                        Tgl Kembali</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">
                                        Kondisi Kembali</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">
                                        Status</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                        <span class="sr-only">Detail</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($item->borrows as $borrow)
                                    <tr>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $borrow->user->name }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $borrow->borrow_date->format('d/m/Y') }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $borrow->condition_at_borrow }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $borrow->return_date ? $borrow->return_date->format('d/m/Y') : '-' }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $borrow->condition_on_return ?? '-' }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span
                                                class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $borrow->status === 'borrowed' ? 'bg-yellow-50 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 ring-1 ring-inset ring-yellow-600/20' : ($borrow->status === 'returned' ? 'bg-green-50 text-green-700 dark:bg-green-900 dark:text-green-200 ring-1 ring-inset ring-green-600/20' : 'bg-red-50 text-red-700 dark:bg-red-900 dark:text-red-200 ring-1 ring-inset ring-red-600/10') }}">
                                                {{ ucfirst($borrow->status) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            <a href="{{ route('borrows.show', $borrow) }}"
                                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition">Detail</a>
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