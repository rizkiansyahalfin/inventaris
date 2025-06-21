@extends('layouts.app')

@section('content')
<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Staff') }}
        </h2>
        @if(auth()->user()->isPetugas())
        <a href="{{ route('staff-reports.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800">
            {{ __('Buat Laporan Baru') }}
        </a>
        @endif
    </div>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Tab Navigation -->
        <div class="mb-6">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button onclick="showTab('list')" id="tab-list" class="tab-button active border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Daftar Laporan
                </button>
                <button onclick="showTab('dashboard')" id="tab-dashboard" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Dashboard
                </button>
                <button onclick="showTab('export')" id="tab-export" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Export
                </button>
                @if(auth()->user()->hasRole('admin'))
                <button onclick="showTab('bulk')" id="tab-bulk" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Aksi Massal
                </button>
                @endif
            </nav>
        </div>

        <!-- Tab Content -->
        <div id="tab-content">
            <!-- Tab 1: Daftar Laporan -->
            <div id="content-list" class="tab-content active">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        @if (session('success'))
                            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                                <p>{{ session('success') }}</p>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                                <p>{{ session('error') }}</p>
                            </div>
                        @endif

                        @if ($staffReports->isEmpty())
                            <div class="text-center py-8">
                                <p class="text-gray-500">Belum ada laporan staff yang dibuat</p>
                                @if(auth()->user()->isPetugas())
                                <div class="mt-4">
                                    <a href="{{ route('staff-reports.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800">
                                        Buat Laporan Pertama
                                    </a>
                                </div>
                                @endif
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border">
                                    <thead>
                                        <tr>
                                            <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Laporan</th>
                                            <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Kerja</th>
                                            <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                                            <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Dibuat</th>
                                            <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewer</th>
                                            <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($staffReports as $report)
                                            <tr>
                                                <td class="py-4 px-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $report->report_date->format('d M Y') }}</div>
                                                </td>
                                                <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $report->hours_worked }} jam
                                                </td>
                                                <td class="py-4 px-4 whitespace-nowrap">
                                                    @if ($report->status === 'draft')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            Draft
                                                        </span>
                                                    @elseif ($report->status === 'submitted')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            Diajukan
                                                        </span>
                                                    @elseif ($report->status === 'reviewed')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Diulas
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $report->user->name }}
                                                </td>
                                                <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $report->created_at->format('d M Y') }}
                                                </td>
                                                <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                                    @if ($report->reviewer)
                                                        {{ $report->reviewer->name }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="py-4 px-4 whitespace-nowrap text-sm font-medium">
                                                    <div class="flex space-x-2">
                                                        <a href="{{ route('staff-reports.show', $report) }}" class="text-blue-600 hover:text-blue-900">Lihat</a>
                                                        
                                                        @if ($report->status === 'draft' && auth()->id() === $report->user_id)
                                                            <a href="{{ route('staff-reports.edit', $report) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                                            
                                                            <button
                                                                onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin menghapus laporan ini?')) document.getElementById('delete-form-{{ $report->id }}').submit();"
                                                                class="text-red-600 hover:text-red-900"
                                                            >
                                                                Hapus
                                                            </button>
                                                            <form id="delete-form-{{ $report->id }}" action="{{ route('staff-reports.destroy', $report) }}" method="POST" class="hidden">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        @endif
                                                        
                                                        @if (auth()->user()->hasRole('admin') && $report->status === 'submitted')
                                                            <a href="{{ route('staff-reports.review', $report) }}" class="text-purple-600 hover:text-purple-900">Ulas</a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-6">
                                {{ $staffReports->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tab 2: Dashboard -->
            <div id="content-dashboard" class="tab-content hidden">
                @include('staff-reports.partials.dashboard-content', ['stats' => $stats, 'recentReports' => $recentReports])
            </div>

            <!-- Tab 3: Export -->
            <div id="content-export" class="tab-content hidden">
                @include('staff-reports.partials.export-content', ['staffReports' => $staffReports, 'users' => $users])
            </div>

            <!-- Tab 4: Bulk Actions (Admin Only) -->
            @if(auth()->user()->hasRole('admin'))
            <div id="content-bulk" class="tab-content hidden">
                @include('staff-reports.partials.bulk-actions-content', ['staffReports' => $staffReports, 'users' => $users])
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.tab-button.active {
    border-color: #3b82f6;
    color: #3b82f6;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}
</style>

<script>
function showTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.add('hidden');
        content.classList.remove('active');
    });

    // Remove active class from all tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('active');
        button.classList.remove('border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab content
    const selectedContent = document.getElementById('content-' + tabName);
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
        selectedContent.classList.add('active');
    }

    // Add active class to selected tab button
    const selectedButton = document.getElementById('tab-' + tabName);
    if (selectedButton) {
        selectedButton.classList.add('active');
        selectedButton.classList.remove('border-transparent', 'text-gray-500');
        selectedButton.classList.add('border-blue-500', 'text-blue-600');
    }
}

// Initialize with list tab
document.addEventListener('DOMContentLoaded', function() {
    showTab('list');
});
</script>
@endsection 