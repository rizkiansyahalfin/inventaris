@extends('layouts.app')

@section('content')
<div class="bg-white overflow-hidden shadow-sm rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Daftar Peminjaman</h2>
            <a href="{{ route('borrows.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                Tambah Peminjaman
            </a>
        </div>

        <!-- Filter -->
        <div class="mb-4">
            <form action="{{ route('borrows.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Cari</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Nama barang atau kode">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Terlambat</option>
                        <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Hilang</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div>
                    <label for="approval_status" class="block text-sm font-medium text-gray-700">Status Persetujuan</label>
                    <select name="approval_status" id="approval_status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                        <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pinjam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persetujuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($borrows as $borrow)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $borrow->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $borrow->item->name }}</div>
                            <div class="text-sm text-gray-500">{{ $borrow->item->code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $borrow->borrow_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="{{ $borrow->due_date < now() && $borrow->status === 'borrowed' ? 'text-red-600 font-medium' : '' }}">
                                {{ $borrow->due_date->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($borrow->status === 'pending')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>
                            @elseif($borrow->status === 'borrowed')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Dipinjam</span>
                            @elseif($borrow->status === 'returned')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Dikembalikan</span>
                            @elseif($borrow->status === 'overdue')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Terlambat</span>
                            @elseif($borrow->status === 'lost')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Hilang</span>
                            @elseif($borrow->status === 'rejected')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($borrow->approval_status === 'pending')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>
                            @elseif($borrow->approval_status === 'approved')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Disetujui</span>
                                @if($borrow->approvedBy)
                                    <div class="text-xs text-gray-500 mt-1">oleh {{ $borrow->approvedBy->name }}</div>
                                @endif
                            @elseif($borrow->approval_status === 'rejected')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                                @if($borrow->approvedBy)
                                    <div class="text-xs text-gray-500 mt-1">oleh {{ $borrow->approvedBy->name }}</div>
                                @endif
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('borrows.show', $borrow) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                            @if(in_array($borrow->status, ['returned', 'lost', 'rejected']))
                            <form action="{{ route('borrows.destroy', $borrow) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus data peminjaman ini?')">
                                    Hapus
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data peminjaman yang ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $borrows->links() }}
        </div>
    </div>
</div>
@endsection 