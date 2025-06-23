@section('navigation')
@php 
    $user = auth()->user();
    $unreadNotifications = $user->notifications()->unread()->count();
    $recentNotifications = $user->notifications()
        ->orderByRaw('read_at IS NULL DESC, created_at DESC')
        ->limit(10)
        ->get();
    $totalNotifications = $user->notifications()->count();
@endphp
<header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6">
        <!-- Left: Hamburger & Breadcrumb -->
        <div class="flex items-center space-x-4">
            <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <!-- Breadcrumb -->
            <nav class="hidden sm:flex items-center space-x-2 text-sm">
                <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Dashboard</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-gray-700 dark:text-gray-300 font-medium">@yield('page_title', 'Beranda')</span>
            </nav>
        </div>
        <!-- Right: Search, Notifications, User -->
        <div class="flex items-center space-x-4">
            <!-- Search -->
            <div class="hidden sm:block relative">
                <input type="text" placeholder="Cari..." class="w-64 pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <!-- Notifications -->
            <div class="relative" x-data="{ open: false }" x-init="
                $watch('open', value => {
                    if (value) {
                        $nextTick(() => {
                            const dropdown = $el.querySelector('.notification-dropdown');
                            const rect = dropdown.getBoundingClientRect();
                            const viewportWidth = window.innerWidth;
                            
                            if (rect.right > viewportWidth) {
                                dropdown.style.right = '0';
                                dropdown.style.left = 'auto';
                            } else if (rect.left < 0) {
                                dropdown.style.left = '0';
                                dropdown.style.right = 'auto';
                            }
                            
                            // Force scroll to work
                            const scrollContainer = dropdown.querySelector('.h-96');
                            if (scrollContainer) {
                                scrollContainer.style.overflowY = 'scroll';
                                scrollContainer.style.height = '24rem';
                                scrollContainer.style.maxHeight = '24rem';
                            }
                        });
                    }
                });
                
                // Add keyboard shortcuts for scrolling
                document.addEventListener('keydown', function(e) {
                    if (open && (e.ctrlKey || e.metaKey)) {
                        const scrollContainer = $el.querySelector('.h-96');
                        if (scrollContainer) {
                            if (e.key === 'ArrowUp') {
                                e.preventDefault();
                                scrollContainer.scrollTop -= 50;
                            } else if (e.key === 'ArrowDown') {
                                e.preventDefault();
                                scrollContainer.scrollTop += 50;
                            }
                        }
                    }
                });
            ">
                <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 relative transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z M11 19H6.5A2.5 2.5 0 0 1 4 16.5v-9A2.5 2.5 0 0 1 6.5 5h11A2.5 2.5 0 0 1 20 7.5V13"></path>
                    </svg>
                    @if($unreadNotifications > 0)
                        <span class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 rounded-full flex items-center justify-center text-xs text-white font-medium">
                            {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
                        </span>
                    @endif
                </button>
                <!-- Dropdown -->
                <div x-show="open" @click.away="open = false" x-transition class="notification-dropdown absolute right-0 mt-2 w-96 sm:w-[28rem] md:w-[32rem] lg:w-[36rem] bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-50 border border-gray-200 dark:border-gray-700">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifikasi</h3>
                            @if($unreadNotifications > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    {{ $unreadNotifications }} baru
                                </span>
                            @endif
                        </div>
                        @if($totalNotifications > 0)
                        <div class="flex items-center space-x-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $totalNotifications }} total</span>
                            <a href="{{ route('notifications.index') }}" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                Lihat Semua
                            </a>
                        </div>
                        @endif
                    </div>
                    <div class="h-96 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 scrollbar-track-gray-100 dark:scrollbar-track-gray-800" style="overflow-y: scroll !important;" x-ref="scrollContainer">
                        @if($recentNotifications->isEmpty())
                            <div class="px-4 py-6 text-center">
                                <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM11 19H6.5A2.5 2.5 0 014 16.5v-9A2.5 2.5 0 016.5 5h11A2.5 2.5 0 0120 7.5V13"></path>
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada notifikasi</p>
                            </div>
                        @else
                            @php
                                $groupedNotifications = $recentNotifications->groupBy(function($notification) {
                                    return $notification->created_at->format('Y-m-d');
                                });
                            @endphp
                            @foreach($groupedNotifications as $date => $notifications)
                                <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-600">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                        @if($date === now()->format('Y-m-d'))
                                            Hari Ini
                                        @elseif($date === now()->subDay()->format('Y-m-d'))
                                            Kemarin
                                        @else
                                            {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                                        @endif
                                    </p>
                                </div>
                                @foreach($notifications as $notification)
                                    <div class="notification-item px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                @if(!$notification->isRead())
                                                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                                @else
                                                    <div class="w-2 h-2 bg-gray-300 dark:bg-gray-600 rounded-full mt-2"></div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0 max-w-full">
                                                <div class="flex flex-col space-y-1">
                                                    <a href="{{ route('notifications.index') }}?notification_id={{ $notification->id }}"
                                                       class="text-sm font-medium text-gray-800 dark:text-gray-200 {{ !$notification->isRead() ? 'font-semibold' : '' }} break-words leading-tight hover:underline">
                                                        {{ $notification->title }}
                                                    </a>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 break-words leading-relaxed">
                                                        {{ $notification->message }}
                                                    </p>
                                                    <div class="flex items-center justify-between">
                                                        <p class="text-xs text-gray-400 dark:text-gray-500">
                                                            {{ $notification->created_at->diffForHumans() }}
                                                        </p>
                                                        @if($notification->data && isset($notification->data['action_link']))
                                                            <a href="{{ $notification->data['action_link'] }}" 
                                                               class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium break-words flex-shrink-0 ml-2">
                                                                {{ $notification->data['action_text'] ?? 'Detail' }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        @endif
                    </div>
                    <!-- Scroll Controls -->
                    <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <button @click="$refs.scrollContainer.scrollTop -= 100" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" title="Scroll Up">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                </svg>
                            </button>
                            <button @click="$refs.scrollContainer.scrollTop += 100" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" title="Scroll Down">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Ctrl+↑/↓ untuk scroll
                        </div>
                    </div>
                    @if($unreadNotifications > 0)
                        <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700">
                            <form action="{{ route('notifications.mark-all-as-read') }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-center">
                                    Tandai Semua Dibaca
                                </button>
                            </form>
                        </div>
                    @endif
                    @if($totalNotifications > 10)
                        <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('notifications.index') }}" class="w-full text-xs text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300 text-center block">
                                Lihat {{ $totalNotifications - 10 }} notifikasi lainnya
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <!-- User Menu -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-2 p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                        <span>{{ strtoupper(substr($user->name,0,1)) }}</span>
                    </div>
                    <span class="hidden md:block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $user->name }}</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <!-- Dropdown -->
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-50 border border-gray-200 dark:border-gray-700">
                    <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                    </div>
                    <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Profil</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Pengaturan</a>
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
@show
