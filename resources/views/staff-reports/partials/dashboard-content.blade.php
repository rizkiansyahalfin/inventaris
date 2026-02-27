<div class="dashboard-content">
    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Laporan</div>
                        <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $stats['total_reports'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Menunggu Review</div>
                        <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $stats['pending_reviews'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Sudah Diulas</div>
                        <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $stats['reviewed_reports'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Jam Kerja</div>
                        <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $stats['total_hours'] ?? '0.0' }} jam</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik dan Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Grafik Status Laporan -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Status Laporan</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Draft</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                <div class="bg-gray-600 dark:bg-gray-500 h-2 rounded-full"
                                    style="width: {{ $stats['draft_percentage'] ?? 0 }}%"></div>
                            </div>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $stats['draft_reports'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Diajukan</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                <div class="bg-yellow-500 dark:bg-yellow-400 h-2 rounded-full"
                                    style="width: {{ $stats['submitted_percentage'] ?? 0 }}%"></div>
                            </div>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $stats['submitted_reports'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Diulas</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                <div class="bg-green-500 dark:bg-green-400 h-2 rounded-full"
                                    style="width: {{ $stats['reviewed_percentage'] ?? 0 }}%"></div>
                            </div>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $stats['reviewed_reports'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Laporan Terbaru</h3>
                <div class="space-y-3">
                    @forelse($recentReports ?? [] as $report)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ optional($report->user)->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $report->report_date ? $report->report_date->format('d M Y') : 'No Date' }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                @php
                                    $status = $report->status ?? 'draft';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($status === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-300
                                    @elseif($status === 'submitted') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @endif">
                                    {{ ucfirst($status) }}
                                </span>
                                <a href="{{ route('staff-reports.show', $report) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 text-sm">
                                    Lihat
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada laporan</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Aksi Cepat</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button onclick="showTab('list')"
                    class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Lihat Semua Laporan</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Kelola semua laporan staff</p>
                    </div>
                </button>

                @if(auth()->user() && auth()->user()->isPetugas())
                    <a href="{{ route('staff-reports.create') }}"
                        class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Buat Laporan Baru</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tambah laporan kerja baru</p>
                        </div>
                    </a>
                @endif

                @if(auth()->user() && auth()->user()->hasRole('admin'))
                    <button onclick="showTab('bulk')"
                        class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Review Laporan</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Laporan yang menunggu review</p>
                        </div>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>