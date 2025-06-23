@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white dark:bg-gray-800 p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">
        {{ $notification->title ?? 'Detail Notifikasi' }}
    </h2>
    <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
        {{ $notification->created_at->translatedFormat('d M Y H:i') }} &middot; 
        @if($notification->read_at)
            <span class="text-green-600 dark:text-green-400">Sudah dibaca</span>
        @else
            <span class="text-red-600 dark:text-red-400">Belum dibaca</span>
        @endif
    </div>
    <div class="mb-6 text-gray-800 dark:text-gray-200 text-base leading-relaxed">
        {{ $notification->message }}
    </div>
    @if($notification->data && isset($notification->data['action_link']))
        <a href="{{ $notification->data['action_link'] }}"
           class="inline-block mb-6 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
            {{ $notification->data['action_text'] ?? 'Lihat Detail' }}
        </a>
    @endif
    <div>
        <a href="{{ route('notifications.index') }}"
           class="inline-block px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
            &larr; Kembali ke Notifikasi
        </a>
    </div>
</div>
@endsection 