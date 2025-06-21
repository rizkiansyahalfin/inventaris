@extends('layouts.app')

@section('content')
<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Laporan Staff') }}
        </h2>
        <div class="flex space-x-2">
            @if ($staffReport->status === 'draft' && auth()->id() === $staffReport->user_id)
                <a href="{{ route('staff-reports.edit', $staffReport) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800">
                    {{ __('Edit') }}
                </a>
            @endif
            
            @if (auth()->user()->hasRole('admin') && $staffReport->status === 'submitted')
                <a href="{{ route('staff-reports.review', $staffReport) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-800">
                    {{ __('Review') }}
                </a>
            @endif
            
            <a href="{{ route('staff-reports.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800">
                {{ __('Kembali') }}
            </a>
        </div>
    </div>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

                <!-- Informasi Dasar -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Laporan</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Laporan</dt>
                                <dd class="text-sm text-gray-900">{{ $staffReport->report_date->format('d M Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Jam Kerja</dt>
                                <dd class="text-sm text-gray-900">{{ $staffReport->hours_worked }} jam</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="text-sm">
                                    @if ($staffReport->status === 'draft')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Draft
                                        </span>
                                    @elseif ($staffReport->status === 'submitted')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Diajukan
                                        </span>
                                    @elseif ($staffReport->status === 'reviewed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Diulas
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pembuat</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dibuat Oleh</dt>
                                <dd class="text-sm text-gray-900">{{ $staffReport->user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Dibuat</dt>
                                <dd class="text-sm text-gray-900">{{ $staffReport->created_at->format('d M Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Terakhir Diupdate</dt>
                                <dd class="text-sm text-gray-900">{{ $staffReport->updated_at->format('d M Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Aktivitas -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Aktivitas yang Dilakukan</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $staffReport->activities }}</p>
                    </div>
                </div>

                <!-- Tantangan -->
                @if ($staffReport->challenges)
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tantangan/Hambatan</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $staffReport->challenges }}</p>
                    </div>
                </div>
                @endif

                <!-- Review (jika sudah diulas) -->
                @if ($staffReport->status === 'reviewed' && $staffReport->review_notes)
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Review dari Admin</h3>
                    <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-400">
                        <div class="mb-2">
                            <span class="text-sm font-medium text-gray-500">Reviewer:</span>
                            <span class="text-sm text-gray-900 ml-2">{{ $staffReport->reviewer->name }}</span>
                        </div>
                        <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $staffReport->review_notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Tombol Aksi -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    @if ($staffReport->status === 'draft' && auth()->id() === $staffReport->user_id)
                        <form action="{{ route('staff-reports.destroy', $staffReport) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800">
                                Hapus
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('staff-reports.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 