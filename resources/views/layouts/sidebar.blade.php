@php
    $user = auth()->user();
    $unreadNotifications = $user->notifications()->unread()->count();
@endphp

<!-- Sidebar Navigation -->
<div class="h-full flex flex-col bg-white dark:bg-gray-800">
    <!-- Logo & Brand -->
    <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200 dark:border-gray-700">
        <div class="sidebar-icon">
            <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2zm0 2v12h16V6H4zm2 2h12v2H6V8zm0 4h8v2H6v-2z"/>
            </svg>
        </div>
        <div class="sidebar-text ml-3">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800 dark:text-gray-200">
                Inventaris
            </a>
        </div>
    </div>

    <!-- Collapse Button (Desktop Only) -->
    <div class="hidden md:flex items-center justify-end px-2 py-2 border-b border-gray-200 dark:border-gray-700">
        <button id="sidebar-collapse" class="p-1.5 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" title="Toggle Sidebar">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
        <!-- Beranda - Prioritas 1 untuk semua role -->
        <a href="{{ route('dashboard') }}" 
           class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
           title="Beranda">
            <div class="sidebar-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <span class="sidebar-text ml-3">Beranda</span>
        </a>

        <!-- Approval Peminjaman - Prioritas 2 untuk Admin -->
        @if($user->hasRole('admin'))
            <a href="{{ route('admin.borrow-approvals.index') }}" 
               class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.borrow-approvals.*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
               title="Approval Peminjaman">
                <div class="sidebar-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="sidebar-text ml-3">Approval Peminjaman</span>
            </a>
        @endif

        <!-- Inventaris - Prioritas 3 untuk semua role -->
        <a href="{{ route('items.index') }}" 
           class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('items.*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
           title="Inventaris">
            <div class="sidebar-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <span class="sidebar-text ml-3">Inventaris</span>
        </a>

        <!-- Peminjaman - Prioritas 4 untuk semua role -->
        <a href="{{ route('borrows.index') }}" 
           class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('borrows.*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
           title="Peminjaman">
            <div class="sidebar-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
            <span class="sidebar-text ml-3">Peminjaman</span>
        </a>

        <!-- Manajemen Pengguna - Prioritas 5 untuk Admin -->
        @if($user->hasRole('admin'))
            <a href="{{ route('admin.users.index') }}" 
               class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
               title="Manajemen Pengguna">
                <div class="sidebar-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <span class="sidebar-text ml-3">Manajemen Pengguna</span>
            </a>
        @endif

        <!-- Kategori - Prioritas 6 untuk Admin -->
        @if($user->hasRole('admin'))
            <a href="{{ route('categories.index') }}" 
               class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('categories.*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
               title="Kategori">
                <div class="sidebar-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <span class="sidebar-text ml-3">Kategori</span>
            </a>
        @endif

        <!-- Laporan - Prioritas 7 untuk Admin & Petugas -->
        @if($user->hasRole('petugas') || $user->hasRole('admin'))
            <a href="{{ route('reports.index') }}" 
               class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('reports.*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
               title="Laporan">
                <div class="sidebar-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="sidebar-text ml-3">Laporan</span>
            </a>
        @endif

        <!-- Pemeliharaan - Prioritas 8 untuk Admin & Petugas -->
        @if($user->hasRole('petugas') || $user->hasRole('admin'))
            <a href="{{ route('maintenances.index') }}" 
               class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('maintenances.*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
               title="Pemeliharaan">
                <div class="sidebar-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a1 1 0 01-1-1V9a1 1 0 011-1h1a2 2 0 100-4H4a1 1 0 01-1-1V4a1 1 0 011-1h3a1 1 0 011 1v1z"/>
                    </svg>
                </div>
                <span class="sidebar-text ml-3">Pemeliharaan</span>
            </a>
        @endif

        <!-- Stok Opname - Prioritas 9 untuk Admin & Petugas -->
        @if($user->hasRole('petugas') || $user->hasRole('admin'))
            <a href="{{ route('stock-opnames.index') }}" 
               class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('stock-opnames.*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
               title="Stok Opname">
                <div class="sidebar-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <span class="sidebar-text ml-3">Stok Opname</span>
            </a>
        @endif

        <!-- Log Aktivitas - Prioritas 10 untuk Admin -->
        @if($user->hasRole('admin'))
            <a href="{{ route('activity-logs.index') }}" 
               class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('activity-logs.*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
               title="Log Aktivitas">
                <div class="sidebar-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="sidebar-text ml-3">Log Aktivitas</span>
            </a>
        @endif

        <!-- Konfigurasi Sistem - Prioritas 11 untuk Admin -->
        @if($user->hasRole('admin'))
            <a href="{{ route('system-configs.index') }}" 
               class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system-configs.*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
               title="Konfigurasi Sistem">
                <div class="sidebar-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="sidebar-text ml-3">Konfigurasi Sistem</span>
            </a>
        @endif

        <!-- Laporan Staf - Prioritas 7 untuk Petugas & Admin -->
        @if($user->hasRole('petugas') || $user->hasRole('admin'))
            <a href="{{ route('staff-reports.index') }}" 
               class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('staff-reports.*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
               title="Laporan Staf">
                <div class="sidebar-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="sidebar-text ml-3">Laporan Staf</span>
            </a>
        @endif

        <!-- Permintaan Barang - Prioritas 12 untuk Admin, 8 untuk Petugas, 4 untuk User -->
        <a href="{{ route('item-requests.index') }}" 
           class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('item-requests.*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
           title="Permintaan Barang">
            <div class="sidebar-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="sidebar-text ml-3">Permintaan Barang</span>
        </a>

        <!-- Notifikasi - Prioritas 13 untuk Admin, 9 untuk Petugas, 5 untuk User -->
        <a href="{{ route('notifications.index') }}" 
           class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('notifications.*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
           title="Notifikasi">
            <div class="sidebar-icon relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM11 19H6.5A2.5 2.5 0 014 16.5v-9A2.5 2.5 0 016.5 5h11A2.5 2.5 0 0120 7.5V13"/>
                </svg>
                @if($unreadNotifications > 0)
                    <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full flex items-center justify-center text-xs text-white font-medium">
                        {{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}
                    </span>
                @else
                    <span class="absolute -top-1 -right-1 h-2 w-2 bg-gray-300 dark:bg-gray-600 rounded-full"></span>
                @endif
            </div>
            <span class="sidebar-text ml-3">Notifikasi</span>
        </a>

        <!-- Umpan Balik - Prioritas 14 untuk Admin, 10 untuk Petugas, 6 untuk User -->
        <a href="{{ route('feedbacks.index') }}" 
           class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('feedbacks.*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
           title="Umpan Balik">
            <div class="sidebar-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <span class="sidebar-text ml-3">Umpan Balik</span>
        </a>

        <!-- Tanda - Prioritas 15 untuk Admin, 11 untuk Petugas, 7 untuk User -->
        <a href="{{ route('bookmarks.index') }}" 
           class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('bookmarks.*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
           title="Tanda">
            <div class="sidebar-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
            </div>
            <span class="sidebar-text ml-3">Tanda</span>
        </a>
    </nav>

    <!-- Footer -->
    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-center">
            <div class="sidebar-icon">
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
            <div class="sidebar-text ml-2">
                <span class="text-xs text-gray-500 dark:text-gray-400">Inventaris v1.0</span>
            </div>
        </div>
    </div>
</div>