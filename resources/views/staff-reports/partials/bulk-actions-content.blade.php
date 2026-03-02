<div class="bulk-content">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            @if (session('success'))
                <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 mb-4"
                    role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 mb-4"
                    role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- Filter untuk memilih laporan -->
            <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Pilih Laporan untuk Aksi Massal
                </h3>
                <form action="{{ route('staff-reports.bulk-actions') }}" method="GET"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label for="status_filter"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select name="status_filter" id="status_filter"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status_filter') == 'draft' ? 'selected' : '' }}>Draft
                            </option>
                            <option value="submitted" {{ request('status_filter') == 'submitted' ? 'selected' : '' }}>
                                Diajukan</option>
                            <option value="reviewed" {{ request('status_filter') == 'reviewed' ? 'selected' : '' }}>Diulas
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="user_filter"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Petugas</label>
                        <select name="user_filter" id="user_filter"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Semua Petugas</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_filter') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dari
                            Tanggal</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sampai
                            Tanggal</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div class="md:col-span-4 flex justify-end space-x-3">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 active:bg-blue-800 dark:active:bg-blue-700 transition-colors">
                            Filter
                        </button>
                        <a href="{{ route('staff-reports.bulk-actions') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 active:bg-gray-800 dark:active:bg-gray-700 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            @if($staffReports->count() > 0)
                <!-- Bulk Actions Form -->
                <form action="{{ route('staff-reports.bulk-process') }}" method="POST" id="bulk-form">
                    @csrf
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="checkbox" id="select-all"
                                        class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Pilih
                                        Semua</span>
                                </label>
                                <span class="text-sm text-gray-500 dark:text-gray-400" id="selected-count">0 laporan
                                    dipilih</span>
                            </div>

                            <div class="flex space-x-3">
                                <select name="bulk_action" id="bulk_action"
                                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="">Pilih Aksi</option>
                                    <option value="approve_all">Setujui Semua</option>
                                    <option value="reject_all">Tolak Semua</option>
                                    <option value="delete_selected">Hapus Terpilih</option>
                                    <option value="export_selected">Export Terpilih</option>
                                </select>

                                <button type="submit" id="bulk-submit" disabled
                                    class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 active:bg-red-800 dark:active:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    Jalankan Aksi
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Laporan List -->
                    <div id="bulk-table-container">
                        @include('staff-reports.partials.bulk-table', ['staffReports' => $staffReports])
                    </div>
                </form>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500">Tidak ada laporan yang ditemukan dengan filter yang dipilih</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Bulk actions filter form
        const bulkFilterForm = document.querySelector('#content-bulk form[action*="bulk-actions"]');
        if (bulkFilterForm) {
            bulkFilterForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);

                fetch('{{ route("staff-reports.bulk-filtered-reports") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('bulk-table-container').innerHTML = data.html;
                        initializeBulkActions();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        }

        // Initialize bulk actions functionality
        function initializeBulkActions() {
            const selectAll = document.getElementById('select-all');
            const headerCheckbox = document.getElementById('header-checkbox');
            const reportCheckboxes = document.querySelectorAll('.report-checkbox');
            const selectedCount = document.getElementById('selected-count');
            const bulkSubmit = document.getElementById('bulk-submit');
            const bulkAction = document.getElementById('bulk_action');

            if (selectAll && headerCheckbox && reportCheckboxes.length > 0) {
                // Select all functionality
                function updateSelectAll() {
                    const checkedBoxes = document.querySelectorAll('.report-checkbox:checked');
                    const totalBoxes = reportCheckboxes.length;

                    selectAll.checked = checkedBoxes.length === totalBoxes;
                    headerCheckbox.checked = checkedBoxes.length === totalBoxes;

                    selectedCount.textContent = `${checkedBoxes.length} laporan dipilih`;

                    // Enable/disable submit button
                    if (bulkSubmit) {
                        bulkSubmit.disabled = checkedBoxes.length === 0 || !bulkAction.value;
                    }
                }

                selectAll.addEventListener('change', function () {
                    reportCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateSelectAll();
                });

                headerCheckbox.addEventListener('change', function () {
                    reportCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateSelectAll();
                });

                reportCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateSelectAll);
                });

                if (bulkAction) {
                    bulkAction.addEventListener('change', function () {
                        const checkedBoxes = document.querySelectorAll('.report-checkbox:checked');
                        if (bulkSubmit) {
                            bulkSubmit.disabled = checkedBoxes.length === 0 || !this.value;
                        }
                    });
                }

                // Confirm before submitting
                const bulkForm = document.getElementById('bulk-form');
                if (bulkForm) {
                    bulkForm.addEventListener('submit', function (e) {
                        const action = bulkAction.value;
                        const checkedBoxes = document.querySelectorAll('.report-checkbox:checked');

                        if (checkedBoxes.length === 0) {
                            e.preventDefault();
                            alert('Pilih setidaknya satu laporan');
                            return;
                        }

                        if (!action) {
                            e.preventDefault();
                            alert('Pilih aksi yang akan dilakukan');
                            return;
                        }

                        let message = '';
                        switch (action) {
                            case 'approve_all':
                                message = `Apakah Anda yakin ingin menyetujui ${checkedBoxes.length} laporan?`;
                                break;
                            case 'reject_all':
                                message = `Apakah Anda yakin ingin menolak ${checkedBoxes.length} laporan?`;
                                break;
                            case 'delete_selected':
                                message = `Apakah Anda yakin ingin menghapus ${checkedBoxes.length} laporan? Tindakan ini tidak dapat dibatalkan.`;
                                break;
                            case 'export_selected':
                                message = `Apakah Anda yakin ingin mengekspor ${checkedBoxes.length} laporan?`;
                                break;
                        }

                        if (!confirm(message)) {
                            e.preventDefault();
                        }
                    });
                }

                // Initialize
                updateSelectAll();
            }
        }

        // Initialize on page load
        initializeBulkActions();
    });
</script>