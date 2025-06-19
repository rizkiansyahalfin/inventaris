@extends('layouts.app')

@section('content')
<div class="p-6">
    <h2 class="text-xl font-semibold mb-4">Laporan Persetujuan Peminjaman</h2>
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-gray-500">Total Permintaan</p>
            <p class="text-2xl font-bold">{{ $reportStats['total_requests'] }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-gray-500">Disetujui</p>
            <p class="text-2xl font-bold">{{ $reportStats['approved_requests'] }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-gray-500">Ditolak</p>
            <p class="text-2xl font-bold">{{ $reportStats['rejected_requests'] }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-gray-500">Menunggu</p>
            <p class="text-2xl font-bold">{{ $reportStats['pending_requests'] }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-gray-500">Dikembalikan</p>
            <p class="text-2xl font-bold">{{ $reportStats['returned_items'] }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-gray-500">Sedang Dipinjam</p>
            <p class="text-2xl font-bold">{{ $reportStats['active_borrows'] }}</p>
        </div>
    </div>
    <div class="overflow-x-auto mt-8">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Ajuan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persetujuan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($borrows as $borrow)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $borrow->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $borrow->user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $borrow->item->name }}</div>
                        <div class="text-sm text-gray-500">{{ $borrow->item->code }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $borrow->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($borrow->status === 'pending')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>
                        @elseif($borrow->status === 'borrowed')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Dipinjam</span>
                        @elseif($borrow->status === 'returned')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Dikembalikan</span>
                        @elseif($borrow->status === 'rejected')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($borrow->approval_status === 'pending')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>
                        @elseif($borrow->approval_status === 'approved')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Disetujui</span>
                        @elseif($borrow->approval_status === 'rejected')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        Tidak ada data peminjaman yang ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection 