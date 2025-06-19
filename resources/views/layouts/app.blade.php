<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Inventaris Pondok') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            /* Custom styles for fixed layout */
            .sidebar-fixed {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                width: 256px; /* w-64 equivalent - expanded */
                z-index: 50; /* Higher than navbar */
                transition: width 0.3s ease-in-out;
                overflow: hidden;
            }
            
            .sidebar-collapsed {
                width: 64px; /* w-16 equivalent - collapsed */
            }
            
            .sidebar-open {
                transform: translateX(0);
            }
            
            .sidebar-closed {
                transform: translateX(-100%);
            }
            
            /* Desktop: sidebar always visible */
            @media (min-width: 769px) {
                .sidebar-fixed {
                    transform: translateX(0) !important;
                }
            }
            
            /* Mobile: sidebar hidden by default */
            @media (max-width: 768px) {
                .sidebar-fixed {
                    transform: translateX(-100%);
                    width: 256px; /* Always full width on mobile */
                }
                .sidebar-collapsed {
                    width: 256px; /* Ignore collapse on mobile */
                }
            }
            
            .main-content-shifted {
                margin-left: 256px; /* Default expanded width */
                transition: margin-left 0.3s ease-in-out;
            }
            
            .main-content-collapsed {
                margin-left: 64px; /* Collapsed width */
            }
            
            .navbar-fixed {
                position: fixed;
                top: 0;
                left: 256px; /* Start from sidebar width on desktop */
                right: 0;
                z-index: 40; /* Lower than sidebar */
                transition: left 0.3s ease-in-out;
            }
            
            .navbar-collapsed {
                left: 64px; /* Adjust for collapsed sidebar */
            }
            
            /* Mobile navbar full width */
            @media (max-width: 768px) {
                .navbar-fixed {
                    left: 0 !important;
                    z-index: 45; /* Higher than sidebar on mobile */
                }
                .main-content-shifted,
                .main-content-collapsed {
                    margin-left: 0 !important;
                }
            }
            
            /* Sidebar content transitions */
            .sidebar-text {
                opacity: 1;
                transition: opacity 0.2s ease-in-out;
            }
            
            .sidebar-collapsed .sidebar-text {
                opacity: 0;
            }
            
            .sidebar-icon {
                min-width: 1.5rem;
                display: flex;
                justify-content: center;
            }
            
            .content-with-navbar {
                padding-top: 4rem; /* Adjust based on navbar height */
            }
            
            @media (max-width: 768px) {
                .main-content-shifted {
                    margin-left: 0;
                }
                
                .navbar-fixed {
                    left: 0;
                    z-index: 45; /* Higher than sidebar on mobile */
                }
                
                .sidebar-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: rgba(0, 0, 0, 0.5);
                    z-index: 30;
                    opacity: 0;
                    visibility: hidden;
                    transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
                }
                
                .sidebar-overlay.active {
                    opacity: 1;
                    visibility: visible;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
        <!-- Sidebar Overlay for mobile -->
        <div id="sidebar-overlay" class="sidebar-overlay md:hidden"></div>
        
        <!-- Top Navigation - Fixed -->
        <nav id="navbar" class="navbar-fixed bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            @include('layouts.navigation')
        </nav>
        
        <!-- Sidebar - Fixed -->
        <aside id="sidebar" class="sidebar-fixed bg-white dark:bg-gray-800 shadow-lg border-r border-gray-200 dark:border-gray-700">
            @include('layouts.sidebar')
        </aside>
        
        <!-- Main Content Area -->
        <div id="main-content" class="min-h-screen content-with-navbar main-content-shifted">
            <!-- Page Heading -->
            @hasSection('header')
                <header class="bg-white dark:bg-gray-800 shadow-sm sticky top-16 z-20">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        @yield('header')
                    </div>
                </header>
            @endif
            
            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6">
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm" role="alert">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm" role="alert">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
                
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm" role="alert">
                        <div class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                
                <!-- Main Content -->
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
        
        <!-- JavaScript for sidebar toggle with persistent state -->
        <script>
            // Global sidebar state management
            window.SidebarState = window.SidebarState || {
                isCollapsed: false,
                
                // Save state (in-memory for current session)
                save: function(collapsed) {
                    this.isCollapsed = collapsed;
                    // Store in sessionStorage as backup if available
                    try {
                        sessionStorage.setItem('sidebar_collapsed', collapsed ? '1' : '0');
                    } catch (e) {
                        // Ignore if sessionStorage not available
                    }
                },
                
                // Load state
                load: function() {
                    // Try to load from sessionStorage first
                    try {
                        const saved = sessionStorage.getItem('sidebar_collapsed');
                        if (saved !== null) {
                            this.isCollapsed = saved === '1';
                            return this.isCollapsed;
                        }
                    } catch (e) {
                        // Ignore if sessionStorage not available
                    }
                    return this.isCollapsed;
                }
            };
            
            document.addEventListener('DOMContentLoaded', function() {
                const sidebar = document.getElementById('sidebar');
                const navbar = document.getElementById('navbar');
                const mainContent = document.getElementById('main-content');
                const overlay = document.getElementById('sidebar-overlay');
                
                // Load saved state and apply it
                const isCollapsed = window.SidebarState.load();
                if (isCollapsed && window.innerWidth >= 769) {
                    sidebar.classList.add('sidebar-collapsed');
                    navbar.classList.add('navbar-collapsed');
                    mainContent.classList.add('main-content-collapsed');
                }
                
                // Multiple possible toggle button IDs/classes
                const toggleSelectors = [
                    '#sidebar-toggle',
                    '#mobile-menu-button', 
                    '.mobile-menu-button',
                    '[data-sidebar-toggle]',
                    'button[aria-label*="menu"]',
                    'button[aria-label*="Menu"]'
                ];
                
                // Desktop collapse button
                const collapseSelectors = [
                    '#sidebar-collapse',
                    '.sidebar-collapse',
                    '[data-sidebar-collapse]'
                ];
                
                // Find toggle buttons
                let toggleBtn = null;
                let collapseBtn = null;
                
                // Try to find mobile toggle button
                for (const selector of toggleSelectors) {
                    toggleBtn = document.querySelector(selector);
                    if (toggleBtn) break;
                }
                
                // Try to find desktop collapse button
                for (const selector of collapseSelectors) {
                    collapseBtn = document.querySelector(selector);
                    if (collapseBtn) break;
                }
                
                // If no specific toggle found, try generic hamburger patterns
                if (!toggleBtn) {
                    // Look for buttons with hamburger-like content
                    const buttons = document.querySelectorAll('button');
                    buttons.forEach(btn => {
                        const hasHamburger = btn.innerHTML.includes('M4 6h16M4 12h16') || 
                                           btn.innerHTML.includes('☰') ||
                                           btn.innerHTML.includes('hamburger') ||
                                           btn.classList.contains('hamburger') ||
                                           btn.getAttribute('aria-label')?.toLowerCase().includes('menu');
                        
                        if (hasHamburger && !toggleBtn) {
                            toggleBtn = btn;
                        }
                    });
                }
                
                // Global toggle function for navbar button
                window.toggleSidebar = function() {
                    if (window.innerWidth >= 769) {
                        // Desktop: collapse/expand
                        const willCollapse = !sidebar.classList.contains('sidebar-collapsed');
                        
                        sidebar.classList.toggle('sidebar-collapsed');
                        navbar.classList.toggle('navbar-collapsed');
                        mainContent.classList.toggle('main-content-collapsed');
                        
                        // Save state
                        window.SidebarState.save(willCollapse);
                    } else {
                        // Mobile: show/hide (don't save this state)
                        sidebar.classList.toggle('sidebar-closed');
                        overlay.classList.toggle('active');
                    }
                };
                
                // Toggle sidebar collapse/expand on desktop
                function toggleSidebarCollapse() {
                    if (window.innerWidth >= 769) {
                        const willCollapse = !sidebar.classList.contains('sidebar-collapsed');
                        
                        sidebar.classList.toggle('sidebar-collapsed');
                        navbar.classList.toggle('navbar-collapsed');
                        mainContent.classList.toggle('main-content-collapsed');
                        
                        // Save state
                        window.SidebarState.save(willCollapse);
                    }
                }
                
                // Toggle sidebar visibility on mobile
                function toggleSidebarMobile() {
                    if (window.innerWidth < 769) {
                        sidebar.classList.toggle('sidebar-closed');
                        overlay.classList.toggle('active');
                    }
                }
                
                // Universal toggle function
                function universalToggle() {
                    window.toggleSidebar();
                }
                
                // Close sidebar when clicking overlay
                overlay?.addEventListener('click', function() {
                    sidebar.classList.add('sidebar-closed');
                    overlay.classList.remove('active');
                });
                
                // Bind events to found buttons
                if (toggleBtn) {
                    // Remove existing event listeners
                    toggleBtn.removeEventListener('click', universalToggle);
                    toggleBtn.addEventListener('click', universalToggle);
                    console.log('Toggle button found and bound:', toggleBtn);
                } else {
                    console.warn('No toggle button found. Add id="sidebar-toggle" to your hamburger menu button.');
                }
                
                if (collapseBtn) {
                    collapseBtn.addEventListener('click', toggleSidebarCollapse);
                    console.log('Collapse button found and bound:', collapseBtn);
                }
                
                // Also bind to any button with onclick="toggleSidebar()"
                document.addEventListener('click', function(e) {
                    if (e.target.getAttribute('onclick') === 'toggleSidebar()' || 
                        e.target.closest('[onclick="toggleSidebar()"]')) {
                        e.preventDefault();
                        universalToggle();
                    }
                });
                
                // Close sidebar on mobile when clicking a link
                const sidebarLinks = sidebar.querySelectorAll('a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 769) {
                            sidebar.classList.add('sidebar-closed');
                            overlay.classList.remove('active');
                        }
                    });
                });
                
                // Handle window resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 769) {
                        // Desktop: ensure sidebar is visible, restore saved state
                        sidebar.classList.remove('sidebar-closed');
                        overlay.classList.remove('active');
                        
                        // Apply saved collapse state
                        const isCollapsed = window.SidebarState.load();
                        if (isCollapsed) {
                            sidebar.classList.add('sidebar-collapsed');
                            navbar.classList.add('navbar-collapsed');
                            mainContent.classList.add('main-content-collapsed');
                        } else {
                            sidebar.classList.remove('sidebar-collapsed');
                            navbar.classList.remove('navbar-collapsed');
                            mainContent.classList.remove('main-content-collapsed');
                        }
                    } else {
                        // Mobile: hide sidebar by default, remove desktop classes
                        sidebar.classList.add('sidebar-closed');
                        sidebar.classList.remove('sidebar-collapsed');
                        navbar.classList.remove('navbar-collapsed');
                        mainContent.classList.remove('main-content-collapsed');
                        overlay.classList.remove('active');
                    }
                });
                
                // Initialize sidebar state based on screen size
                if (window.innerWidth < 769) {
                    sidebar.classList.add('sidebar-closed');
                } else {
                    // Apply saved state on desktop
                    const isCollapsed = window.SidebarState.load();
                    if (isCollapsed) {
                        sidebar.classList.add('sidebar-collapsed');
                        navbar.classList.add('navbar-collapsed');
                        mainContent.classList.add('main-content-collapsed');
                    }
                }
            });
        </script>
        
        @stack('scripts')
    </body>
</html>