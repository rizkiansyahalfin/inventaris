@extends('layouts.app')

@section('content')
@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Laporan Staff') }}
        </h2>
        <div class="flex space-x-2">
            @if ($staffReport->status === 'draft' && auth()->id() === $staffReport->user_id)
                <a href="{{ route('staff-reports.edit', $staffReport) }}"
                    class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 active:bg-green-800 dark:active:bg-green-700 transition-colors">
                    {{ __('Edit') }}
                </a>
            @endif

            @if (auth()->user() && auth()->user()->hasRole('admin') && $staffReport->status === 'submitted')
                <a href="{{ route('staff-reports.review.form', $staffReport) }}"
                    class="inline-flex items-center px-4 py-2 bg-purple-600 dark:bg-purple-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 dark:hover:bg-purple-600 active:bg-purple-800 dark:active:bg-purple-700 transition-colors">
                    {{ __('Ulas') }}
                </a>
            @endif

            <a href="{{ route('staff-reports.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 active:bg-gray-800 dark:active:bg-gray-700 transition-colors">
                {{ __('Kembali') }}
            </a>
        </div>
    </div>
@endsection

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 dark:border-green-800 text-green-700 dark:text-green-300 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 dark:border-red-800 text-red-700 dark:text-red-300 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <!-- Informasi Dasar -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informasi Laporan</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Laporan</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $staffReport->report_date->format('d M Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jam Kerja</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $staffReport->hours_worked }}
                                        jam</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                    <dd class="text-sm">
                                        @if ($staffReport->status === 'draft')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                Draft
                                            </span>
                                        @elseif ($staffReport->status === 'submitted')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                Diajukan
                                            </span>
                                        @elseif ($staffReport->status === 'reviewed')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Diulas
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informasi Pembuat</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibuat Oleh</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $staffReport->user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Dibuat</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $staffReport->created_at->format('d M Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Terakhir Diupdate</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $staffReport->updated_at->format('d M Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Aktivitas -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Aktivitas yang Dilakukan</h3>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                                {{ $staffReport->activities }}</p>
                        </div>
                    </div>

                    <!-- Tantangan -->
                    @if ($staffReport->challenges)
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Tantangan/Hambatan</h3>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                                    {{ $staffReport->challenges }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Review (jika sudah diulas) -->
                    @if ($staffReport->status === 'reviewed' && $staffReport->review_notes)
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Review dari Admin</h3>
                            <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg border-l-4 border-blue-400">
                                <div class="mb-2">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Reviewer:</span>
                                    <span
                                        class="text-sm text-gray-900 dark:text-gray-100 ml-2">{{ optional($staffReport->reviewer)->name ?? 'Unknown' }}</span>
                                </div>
                                <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                                    {{ $staffReport->review_notes }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Tombol Aksi -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        @if ($staffReport->status === 'draft' && auth()->id() === $staffReport->user_id)
                            <form action="{{ route('staff-reports.destroy', $staffReport) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 active:bg-red-800 dark:active:bg-red-700 transition-colors">
                                    Hapus
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('staff-reports.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 active:bg-gray-800 dark:active:bg-gray-700 transition-colors">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection