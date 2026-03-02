@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Persetujuan Peminjaman Menunggu</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.borrow-approvals.index') }}"
                            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                            Dashboard
                        </a>
                        <form action="{{ route('admin.borrow-approvals.bulk-approve') }}" method="POST"
                            id="bulk-approve-form" class="inline">
                            @csrf
                            <button type="submit"
                                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors"
                                onclick="return confirm('Setujui semua yang dipilih?')">
                                Setujui Terpilih
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tabel -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <input type="checkbox" id="select-all"
                                        class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Peminjam</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Barang</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Jumlah</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Tanggal Ajuan</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($borrows as $borrow)
                                <tr>
                                    <td class="px-6 py-4">
                                        <input type="checkbox" name="borrow_ids[]" value="{{ $borrow->id }}"
                                            form="bulk-approve-form"
                                            class="borrow-checkbox rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $borrow->user->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $borrow->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $borrow->item->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $borrow->item->code }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                        {{ $borrow->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                        {{ $borrow->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <form action="{{ route('borrows.approve', $borrow) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="text-green-600 dark:text-green-400 hover:text-green-900 mr-3 font-medium"
                                                onclick="return confirm('Setujui peminjaman ini?')">
                                                Setujui
                                            </button>
                                        </form>
                                        <button type="button" onclick="showRejectModal({{ $borrow->id }})"
                                            class="text-red-600 dark:text-red-400 hover:text-red-900 font-medium">
                                            Tolak
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada pengajuan peminjaman menunggu
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
        <div
            class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800 dark:border-gray-700">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Tolak Peminjaman</h3>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="rejection_reason"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alasan Penolakan</label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="3" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Masukkan alasan penolakan..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')"
                            class="bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
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

            document.getElementById('select-all').addEventListener('change', function () {
                const checkboxes = document.querySelectorAll('.borrow-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
            });
        </script>
    @endpush
@endsection