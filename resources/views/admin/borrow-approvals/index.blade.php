@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Menunggu Persetujuan</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_pending'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Disetujui</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_approved'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Ditolak</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_rejected'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Sedang Dipinjam</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_borrowed'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Dikembalikan</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_returned'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter dan Pencarian -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Kelola Persetujuan Peminjaman</h2>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.borrow-approvals.pending') }}" class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700">
                        Menunggu Persetujuan
                    </a>
                    <a href="{{ route('admin.borrow-approvals.report') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Laporan
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.borrow-approvals.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Cari</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Nama barang, kode, atau peminjam">
                </div>

                <div>
                    <label for="approval_status" class="block text-sm font-medium text-gray-700">Status Persetujuan</label>
                    <select name="approval_status" id="approval_status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status Peminjaman</label>
                    <select name="status" id="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200">
                        Filter
                    </button>
                </div>
            </form>

            <!-- Tabel -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Ajuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persetujuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
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
                                @if($borrow->approval_status === 'pending')
                                    <form action="{{ route('borrows.approve', $borrow) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900 mr-2" onclick="return confirm('Setujui peminjaman ini?')">
                                            Setujui
                                        </button>
                                    </form>
                                    <button type="button" onclick="showRejectModal({{ $borrow->id }})" class="text-red-600 hover:text-red-900">
                                        Tolak
                                    </button>
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
</div>

<!-- Modal Reject -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tolak Peminjaman</h3>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Alasan Penolakan</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="3" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Masukkan alasan penolakan..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                        onclick="document.getElementById('rejectModal').classList.add('hidden')"
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" 
                        class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showRejectModal(borrowId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    form.action = `/borrows/${borrowId}/reject`;
    modal.classList.remove('hidden');
}
</script>
@endpush

@endsection 