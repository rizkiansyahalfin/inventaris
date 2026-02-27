@extends('layouts.app')

@section('page_title', 'Notifikasi')

@push('styles')
    <style>
        @keyframes pulse-ring {
            0% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
            }
        }

        .highlight-notification {
            animation: pulse-ring 1.5s ease-out;
            border-color: rgba(59, 130, 246, 0.8) !important;
            background-color: rgba(59, 130, 246, 0.1);
        }
    </style>
@endpush

@section('content')
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Notifikasi</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Daftar semua notifikasi Anda.</p>
                        </div>
                        @if($notifications->count() > 0)
                            <form action="{{ route('notifications.clear-all') }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua notifikasi?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Hapus Semua
                                </button>
                            </form>
                        @endif
                    </div>

                    @if($notifications->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-5 5v-5zM11 19H6.5A2.5 2.5 0 014 16.5v-9A2.5 2.5 0 016.5 5h11A2.5 2.5 0 0120 7.5V13">
                                </path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada notifikasi</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Anda akan melihat notifikasi di sini ketika
                                ada pembaruan.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($notifications as $notification)
                                <div id="notification-{{ $notification->id }}"
                                    class="notification-item p-4 border {{ $notification->isRead() ? 'border-gray-200 dark:border-gray-700' : 'border-blue-300 dark:border-blue-600' }} rounded-lg flex items-start space-x-4 transition-colors duration-300">
                                    <div class="flex-shrink-0 pt-1">
                                        @if($notification->isRead())
                                            <div class="w-3 h-3 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                                        @else
                                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $notification->title }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $notification->message }}</p>
                                        <div class="mt-3 flex items-center space-x-4">
                                            @if(!$notification->isRead())
                                                <form action="{{ route('notifications.mark-as-read', $notification) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">Tandai
                                                        Dibaca</button>
                                                </form>
                                            @endif
                                            <form action="{{ route('notifications.destroy', $notification) }}" method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-xs font-medium text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-500">Hapus</button>
                                            </form>
                                            @if($notification->data && isset($notification->data['action_link']))
                                                <a href="{{ $notification->data['action_link'] }}"
                                                    class="text-xs font-medium text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">
                                                    {{ $notification->data['action_text'] ?? 'Lihat Detail' }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-8">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const notificationId = urlParams.get('notification_id');

            if (notificationId) {
                const notificationElement = document.getElementById('notification-' + notificationId);
                if (notificationElement) {
                    // Scroll to the element
                    notificationElement.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    // Add highlight class
                    notificationElement.classList.add('highlight-notification');

                    // Remove highlight after animation ends
                    setTimeout(() => {
                        notificationElement.classList.remove('highlight-notification');
                    }, 2000);
                }
            }
        });
    </script>
@endpush