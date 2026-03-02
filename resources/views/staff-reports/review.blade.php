@extends('layouts.app')

@section('content')
    @section('header')
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Review Laporan Staff') }}
            </h2>
            <a href="{{ route('staff-reports.show', $staffReport) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800">
                {{ __('Kembali') }}
            </a>
        </div>
    @endsection

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Informasi Laporan -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informasi Laporan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Pembuat:</span>
                                <span
                                    class="text-sm text-gray-900 dark:text-gray-100 ml-2">{{ $staffReport->user->name }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Laporan:</span>
                                <span
                                    class="text-sm text-gray-900 dark:text-gray-100 ml-2">{{ $staffReport->report_date->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Jam Kerja:</span>
                                <span class="text-sm text-gray-900 dark:text-gray-100 ml-2">{{ $staffReport->hours_worked }}
                                    jam</span>
                            </div>
                        </div>
                    </div>

                    <!-- Aktivitas -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Aktivitas yang Dilakukan</h3>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                                {{ $staffReport->activities }}
                            </p>
                        </div>
                    </div>

                    <!-- Tantangan -->
                    @if ($staffReport->challenges)
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Tantangan/Hambatan</h3>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                                    {{ $staffReport->challenges }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Form Review -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Review Laporan</h3>
                        <form action="{{ route('staff-reports.review', $staffReport) }}" method="POST">
                            @csrf

                            <div>
                                <label for="review_notes"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan Review
                                    *</label>
                                <textarea name="review_notes" id="review_notes" rows="6"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    placeholder="Berikan feedback dan catatan review untuk laporan ini..."
                                    required>{{ old('review_notes') }}</textarea>
                                @error('review_notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tombol Submit -->
                            <div class="mt-6 flex justify-end space-x-3">
                                <a href="{{ route('staff-reports.show', $staffReport) }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 active:bg-gray-800 dark:active:bg-gray-700 transition-colors">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 dark:bg-purple-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 dark:hover:bg-purple-600 active:bg-purple-800 dark:active:bg-purple-700 transition-colors">
                                    Submit Review
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection