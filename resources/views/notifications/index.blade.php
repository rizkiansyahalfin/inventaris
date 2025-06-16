<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Notifikasi') }}
            </h2>
            <form action="{{ route('notifications.mark-all-as-read') }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Tandai Semua Dibaca') }}
                </button>
            </form>
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

                    @if ($notifications->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500">Tidak ada notifikasi</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($notifications as $notification)
                                <div class="border-l-4 p-4 {{ $notification->is_read ? 'border-gray-200 bg-gray-50' : 'border-blue-500 bg-blue-50' }}">
                                    <div class="flex justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold {{ $notification->is_read ? 'text-gray-700' : 'text-blue-800' }}">
                                                {{ $notification->title }}
                                            </h3>
                                            <p class="mt-1 text-sm {{ $notification->is_read ? 'text-gray-600' : 'text-blue-700' }}">
                                                {{ $notification->message }}
                                            </p>
                                            <div class="mt-2 text-xs text-gray-500">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </div>

                                            @if ($notification->action_link)
                                                <div class="mt-2">
                                                    <a href="{{ $notification->action_link }}" class="text-sm text-blue-600 hover:text-blue-800">
                                                        {{ $notification->action_text ?? 'Lihat Detail' }}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex items-start space-x-2">
                                            @if (!$notification->is_read)
                                                <form action="{{ route('notifications.mark-as-read', $notification) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">
                                                        Tandai Dibaca
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('notifications.destroy', $notification) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-600 hover:text-red-800">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 