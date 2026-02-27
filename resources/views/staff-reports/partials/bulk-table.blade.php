@if($staffReports->count() > 0)
    <!-- Laporan List -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white dark:bg-gray-800 border dark:border-gray-700">
            <thead>
                <tr>
                    <th
                        class="py-3 px-4 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <input type="checkbox" id="header-checkbox"
                            class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </th>
                    <th
                        class="py-3 px-4 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Tanggal</th>
                    <th
                        class="py-3 px-4 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Petugas</th>
                    <th
                        class="py-3 px-4 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Jam Kerja</th>
                    <th
                        class="py-3 px-4 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Status</th>
                    <th
                        class="py-3 px-4 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($staffReports as $report)
                    <tr>
                        <td class="py-4 px-4">
                            <input type="checkbox" name="selected_reports[]" value="{{ $report->id }}"
                                class="report-checkbox rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </td>
                        <td class="py-4 px-4 text-sm text-gray-900 dark:text-gray-300">
                            {{ $report->report_date->format('d M Y') }}
                        </td>
                        <td class="py-4 px-4 text-sm text-gray-900 dark:text-gray-300">
                            {{ $report->user->name }}
                        </td>
                        <td class="py-4 px-4 text-sm text-gray-900 dark:text-gray-300">
                            {{ $report->hours_worked }} jam
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($report->status === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                    @elseif($report->status === 'submitted') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @endif">
                                {{ ucfirst($report->status) }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-sm font-medium">
                            <a href="{{ route('staff-reports.show', $report) }}"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">Lihat</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $staffReports->appends(request()->query())->links() }}
    </div>
@else
    <div class="text-center py-8">
        <p class="text-gray-500">Tidak ada laporan yang ditemukan dengan filter yang dipilih</p>
    </div>
@endif