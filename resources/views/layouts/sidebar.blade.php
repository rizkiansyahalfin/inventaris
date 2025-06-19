@php
    $user = auth()->user();
@endphp
<aside x-data="{ open: false }" class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col transition-transform duration-200 transform -translate-x-full md:translate-x-0 md:static md:inset-0" :class="{ '-translate-x-full': !open, 'translate-x-0': open }" @keydown.window.escape="open = false">
    <!-- Logo & Close Button -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-800">
        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800 dark:text-gray-200">Inventaris</a>
        <button class="md:hidden p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800" @click="open = false">
            <svg class="h-6 w-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>
    <!-- Menu -->
    <nav class="flex-1 px-2 py-4 space-y-2 overflow-y-auto">
        <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('dashboard') ? 'bg-gray-200 dark:bg-gray-800 font-semibold' : '' }}">Beranda</a>
        <a href="{{ route('items.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('items.*') ? 'bg-gray-200 dark:bg-gray-800 font-semibold' : '' }}">Inventaris</a>
        <a href="{{ route('borrows.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('borrows.*') ? 'bg-gray-200 dark:bg-gray-800 font-semibold' : '' }}">Peminjaman</a>
        @if($user->hasRole('admin'))
            <a href="{{ route('categories.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('categories.*') ? 'bg-gray-200 dark:bg-gray-800 font-semibold' : '' }}">Kategori</a>
            <a href="{{ route('admin.borrow-approvals.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('admin.borrow-approvals.*') ? 'bg-gray-200 dark:bg-gray-800 font-semibold' : '' }}">Approval Peminjaman</a>
            <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('admin.users.*') ? 'bg-gray-200 dark:bg-gray-800 font-semibold' : '' }}">Manajemen Pengguna</a>
            <a href="{{ route('activity-logs.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('activity-logs.*') ? 'bg-gray-200 dark:bg-gray-800 font-semibold' : '' }}">Log Aktivitas</a>
            <a href="{{ route('system-configs.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('system-configs.*') ? 'bg-gray-200 dark:bg-gray-800 font-semibold' : '' }}">Konfigurasi Sistem</a>
        @endif
        @if($user->hasRole('petugas') || $user->hasRole('admin'))
            <a href="{{ route('maintenances.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('maintenances.*') ? 'bg-gray-200 dark:bg-gray-800 font-semibold' : '' }}">Pemeliharaan</a>
            <a href="{{ route('stock-opnames.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('stock-opnames.*') ? 'bg-gray-200 dark:bg-gray-800 font-semibold' : '' }}">Stok Opname</a>
            <a href="{{ route('reports.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('reports.*') ? 'bg-gray-200 dark:bg-gray-800 font-semibold' : '' }}">Laporan</a>
        @endif
        <a href="{{ route('bookmarks.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('bookmarks.*') ? 'bg-gray-200 dark:bg-gray-800 font-semibold' : '' }}">Tanda</a>
        <a href="{{ route('feedbacks.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('feedbacks.*') ? 'bg-gray-200 dark:bg-gray-800 font-semibold' : '' }}">Umpan Balik</a>
        <a href="{{ route('item-requests.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('item-requests.*') ? 'bg-gray-200 dark:bg-gray-800 font-semibold' : '' }}">Permintaan Barang</a>
        <a href="{{ route('notifications.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('notifications.*') ? 'bg-gray-200 dark:bg-gray-800 font-semibold' : '' }}">Notifikasi</a>
        @if($user->hasRole('petugas'))
            <a href="{{ route('staff-reports.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('staff-reports.*') ? 'bg-gray-200 dark:bg-gray-800 font-semibold' : '' }}">Laporan Staf</a>
        @endif
    </nav>
    <!-- Footer -->
    <div class="p-4 border-t border-gray-200 dark:border-gray-800 text-xs text-gray-500 dark:text-gray-400">
        <span>Inventaris v1.0</span>
    </div>
</aside>
<!-- Toggle Button for Mobile -->
<button class="fixed z-40 bottom-6 left-6 md:hidden bg-blue-600 text-white p-3 rounded-full shadow-lg focus:outline-none" @click="open = !open">
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
</button>
