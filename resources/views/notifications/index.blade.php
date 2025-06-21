@extends('layouts.app')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Notifikasi</h2>
@endsection
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <!-- Header with actions -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Daftar Notifikasi</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $notifications->total() }} notifikasi total
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        💡 Gunakan mouse wheel, tombol navigasi, atau Ctrl+↑/↓ untuk scroll
                    </p>
                </div>
                @if($notifications->where('read_at', null)->count() > 0)
                    <form action="{{ route('notifications.mark-all-as-read') }}" method="POST" class="flex items-center space-x-3">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 dark:text-blue-300 dark:bg-blue-900 dark:hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Tandai Semua Dibaca
                        </button>
                    </form>
                @endif
            </div>

            @if (session('success'))
                <div class="px-6 py-4 bg-green-50 dark:bg-green-900/20 border-b border-green-200 dark:border-green-800">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if ($notifications->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM11 19H6.5A2.5 2.5 0 014 16.5v-9A2.5 2.5 0 016.5 5h11A2.5 2.5 0 0120 7.5V13"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada notifikasi</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Anda akan melihat notifikasi di sini ketika ada aktivitas baru.</p>
                </div>
            @else
                <!-- Container dengan tinggi tetap dan scroll yang jelas -->
                <div class="relative">
                    <div class="notification-scroll-container h-96 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 scrollbar-track-gray-100 dark:scrollbar-track-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                        @php
                            $groupedNotifications = $notifications->groupBy(function($notification) {
                                return $notification->created_at->format('Y-m-d');
                            });
                        @endphp
                        @foreach($groupedNotifications as $date => $dateNotifications)
                            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600 sticky top-0 z-10">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    @if($date === now()->format('Y-m-d'))
                                        Hari Ini
                                    @elseif($date === now()->subDay()->format('Y-m-d'))
                                        Kemarin
                                    @else
                                        {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                                    @endif
                                </h4>
                            </div>
                            @foreach ($dateNotifications as $notification)
                                <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-200 dark:border-gray-700 {{ !$notification->isRead() ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}">
                                    <div class="flex items-start space-x-4">
                                        <!-- Status indicator -->
                                        <div class="flex-shrink-0">
                                            @if(!$notification->isRead())
                                                <div class="w-3 h-3 bg-blue-500 rounded-full mt-2"></div>
                                            @else
                                                <div class="w-3 h-3 bg-gray-300 dark:bg-gray-600 rounded-full mt-2"></div>
                                            @endif
                                        </div>
                                        
                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 {{ !$notification->isRead() ? 'font-semibold' : '' }}">
                                                        {{ $notification->title }}
                                                    </h4>
                                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                                        {{ $notification->message }}
                                                    </p>
                                                    <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                                        <span>{{ $notification->created_at->diffForHumans() }}</span>
                                                        @if($notification->type)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                                {{ ucfirst($notification->type) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <!-- Actions -->
                                                <div class="flex items-center space-x-2 ml-4">
                                                    @if (!$notification->isRead())
                                                        <form action="{{ route('notifications.mark-as-read', $notification) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                                                Tandai Dibaca
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium" 
                                                                onclick="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            
                                            @if ($notification->data && isset($notification->data['action_link']))
                                                <div class="mt-3">
                                                    <a href="{{ $notification->data['action_link'] }}" 
                                                       class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                                        {{ $notification->data['action_text'] ?? 'Lihat Detail' }}
                                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                        
                        <!-- Indikator scroll di bagian bawah -->
                        <div class="px-6 py-2 text-center text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                            </svg>
                            Scroll untuk melihat lebih banyak notifikasi
                        </div>
                        
                        <!-- Indikator scroll di bagian atas -->
                        <div class="px-6 py-2 text-center text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                            Gunakan scroll atau tombol navigasi
                        </div>
                    </div>
                    
                    <!-- Tombol scroll ke atas dan bawah -->
                    <div class="absolute right-2 top-2 space-y-1">
                        <button onclick="scrollToTop()" class="p-1 bg-white dark:bg-gray-700 rounded-full shadow-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors" title="Scroll ke atas (Ctrl+↑)">
                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                        </button>
                        <button onclick="scrollToBottom()" class="p-1 bg-white dark:bg-gray-700 rounded-full shadow-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors" title="Scroll ke bawah (Ctrl+↓)">
                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Indikator scroll di pojok kanan bawah -->
                    <div class="absolute right-2 bottom-2">
                        <div class="bg-white dark:bg-gray-700 rounded-full shadow-lg border border-gray-200 dark:border-gray-600 p-1">
                            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Menampilkan {{ $notifications->firstItem() ?? 0 }} - {{ $notifications->lastItem() ?? 0 }} dari {{ $notifications->total() }} notifikasi
                            </div>
                            <div class="flex items-center space-x-2">
                                {{ $notifications->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function scrollToTop() {
        const container = document.querySelector('.overflow-y-auto');
        if (container) {
            container.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    }
    
    function scrollToBottom() {
        const container = document.querySelector('.overflow-y-auto');
        if (container) {
            container.scrollTo({
                top: container.scrollHeight,
                behavior: 'smooth'
            });
        }
    }
    
    // Tambahkan event listener untuk scroll dengan keyboard
    document.addEventListener('keydown', function(e) {
        const container = document.querySelector('.overflow-y-auto');
        if (!container) return;
        
        if (e.key === 'ArrowUp' && e.ctrlKey) {
            e.preventDefault();
            scrollToTop();
        } else if (e.key === 'ArrowDown' && e.ctrlKey) {
            e.preventDefault();
            scrollToBottom();
        }
    });
    
    // Tambahkan smooth scroll untuk mouse wheel
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.overflow-y-auto');
        if (container) {
            container.addEventListener('wheel', function(e) {
                e.preventDefault();
                container.scrollTop += e.deltaY;
            }, { passive: false });
        }
    });
</script>
@endpush 