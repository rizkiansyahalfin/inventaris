@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profil Saya') }}
        </h2
    </div>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="flex-shrink-0">
                            <div class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <span class="text-3xl text-gray-500 dark:text-gray-400">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold">{{ auth()->user()->name }}</h3>
                            <p class="text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Role: {{ ucfirst(auth()->user()->role) }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="font-semibold mb-2">Informasi Dasar</h4>
                            <div class="space-y-2">
                                <p><span class="text-gray-500 dark:text-gray-400">Nama:</span> {{ auth()->user()->name }}</p>
                                <p><span class="text-gray-500 dark:text-gray-400">Email:</span> {{ auth()->user()->email }}</p>
                                <p><span class="text-gray-500 dark:text-gray-400">Role:</span> {{ ucfirst(auth()->user()->role) }}</p>
                                <p><span class="text-gray-500 dark:text-gray-400">Bergabung:</span> {{ auth()->user()->created_at->format('d F Y') }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="font-semibold mb-2">Statistik</h4>
                            <div class="space-y-2">
                                <p><span class="text-gray-500 dark:text-gray-400">Total Peminjaman:</span> {{ auth()->user()->borrows()->count() }}</p>
                                <p><span class="text-gray-500 dark:text-gray-400">Peminjaman Aktif:</span> {{ auth()->user()->borrows()->where('status', 'active')->count() }}</p>
                                <p><span class="text-gray-500 dark:text-gray-400">Total Feedback:</span> {{ auth()->user()->feedback()->count() }}</p>
                                <p><span class="text-gray-500 dark:text-gray-400">Total Bookmark:</span> {{ auth()->user()->bookmarks()->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('profile.show') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Lihat Detail
                        </a>
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 