@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Laporan Persetujuan Peminjaman</h2>
            <a href="{{ route('admin.borrow-approvals.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 transition-colors">
                <p class="text-gray-500 dark:text-gray-400">Total Permintaan</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $reportStats['total_requests'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 transition-colors">
                <p class="text-gray-500 dark:text-gray-400">Disetujui</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $reportStats['approved_requests'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 transition-colors">
                <p class="text-gray-500 dark:text-gray-400">Ditolak</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $reportStats['rejected_requests'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 transition-colors">
                <p class="text-gray-500 dark:text-gray-400">Menunggu</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $reportStats['pending_requests'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 transition-colors">
                <p class="text-gray-500 dark:text-gray-400">Dikembalikan</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $reportStats['returned_items'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 transition-colors">
                <p class="text-gray-500 dark:text-gray-400">Sedang Dipinjam</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $reportStats['active_borrows'] }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Peminjam</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Barang</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Tanggal Ajuan</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Persetujuan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($borrows as $borrow)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $borrow->user->name }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $borrow->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $borrow->item->name }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $borrow->item->code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                    {{ $borrow->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($borrow->status === 'pending')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Menunggu</span>
                                    @elseif($borrow->status === 'borrowed')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Dipinjam</span>
                                    @elseif($borrow->status === 'returned')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Dikembalikan</span>
                                    @elseif($borrow->status === 'rejected')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($borrow->approval_status === 'pending')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Menunggu</span>
                                    @elseif($borrow->approval_status === 'approved')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Disetujui</span>
                                    @elseif($borrow->approval_status === 'rejected')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada data peminjaman yang ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
@endsection