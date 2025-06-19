@extends('layouts.app')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Bookmarks</h2>
@endsection
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            @if ($bookmarks->isEmpty())
                <div class="text-center py-8">
                    <p class="text-gray-500">Anda belum memiliki bookmark</p>
                    <a href="{{ route('items.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        {{ __('Jelajahi Inventaris') }}
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($bookmarks as $bookmark)
                        <div class="border rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                            <div class="relative pb-48">
                                @if ($bookmark->item->image)
                                    <img class="absolute h-full w-full object-cover" src="{{ asset('storage/' . $bookmark->item->image) }}" alt="{{ $bookmark->item->name }}">
                                @else
                                    <div class="absolute h-full w-full flex items-center justify-center bg-gray-100">
                                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <button
                                    onclick="document.getElementById('toggle-bookmark-{{ $bookmark->id }}').submit();"
                                    class="absolute top-2 right-2 p-1 bg-white rounded-full shadow hover:bg-gray-100"
                                >
                                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </button>
                                <form id="toggle-bookmark-{{ $bookmark->id }}" action="{{ route('bookmarks.destroy', $bookmark) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-lg mb-1 truncate">{{ $bookmark->item->name }}</h3>
                                <p class="text-sm text-gray-500 mb-2 truncate">Kode: {{ $bookmark->item->code ?? '-' }}</p>
                                <p class="text-sm mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($bookmark->item->status === 'available') bg-green-100 text-green-800 
                                        @elseif($bookmark->item->status === 'borrowed') bg-yellow-100 text-yellow-800 
                                        @elseif($bookmark->item->status === 'maintenance') bg-blue-100 text-blue-800 
                                        @else bg-red-100 text-red-800 
                                        @endif">
                                        {{ ucfirst($bookmark->item->status) }}
                                    </span>
                                </p>
                                <div class="flex justify-end mt-2">
                                    <a href="{{ route('items.show', $bookmark->item) }}" class="text-sm text-blue-600 hover:text-blue-800">
                                        {{ __('Lihat Detail') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $bookmarks->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 