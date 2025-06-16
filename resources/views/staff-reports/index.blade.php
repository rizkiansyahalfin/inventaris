<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Laporan Staff') }}
            </h2>
            <a href="{{ route('staff-reports.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800">
                {{ __('Buat Laporan Baru') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('status'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('status') }}</p>
                        </div>
                    @endif

                    @if ($staffReports->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500">Belum ada laporan staff yang dibuat</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border">
                                <thead>
                                    <tr>
                                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Review</th>
                                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($staffReports as $report)
                                        <tr>
                                            <td class="py-4 px-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $report->title }}</div>
                                            </td>
                                            <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $report->category }}
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
                                                @if ($report->reviewed_at)
                                                    {{ $report->reviewed_at->format('d M Y') }}
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
    </div>
</x-app-layout> 