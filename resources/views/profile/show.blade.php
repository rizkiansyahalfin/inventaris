@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Profil') }}
        </h2>
        <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
            Edit Profil
        </a>
    </div>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4">
                        <a href="{{ route('profile.index') }}" class="inline-block bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200">
                            Kembali ke Daftar Profil
                        </a>
                    </div>
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
                            <h4 class="font-semibold mb-2">Informasi Pribadi</h4>
                            <div class="space-y-2">
                                <p><span class="text-gray-500 dark:text-gray-400">Nama:</span> {{ auth()->user()->name }}</p>
                                <p><span class="text-gray-500 dark:text-gray-400">Email:</span> {{ auth()->user()->email }}</p>
                                <p><span class="text-gray-500 dark:text-gray-400">Role:</span> {{ ucfirst(auth()->user()->role) }}</p>
                                <p><span class="text-gray-500 dark:text-gray-400">Bergabung:</span> {{ auth()->user()->created_at->format('d F Y') }}</p>
                                <p><span class="text-gray-500 dark:text-gray-400">Terakhir Update:</span> {{ auth()->user()->updated_at->format('d F Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="font-semibold mb-2">Statistik Aktivitas</h4>
                            <div class="space-y-2">
                                <p><span class="text-gray-500 dark:text-gray-400">Total Peminjaman:</span> {{ auth()->user()->borrows()->count() }}</p>
                                <p><span class="text-gray-500 dark:text-gray-400">Peminjaman Aktif:</span> {{ auth()->user()->borrows()->where('status', 'active')->count() }}</p>
                                <p><span class="text-gray-500 dark:text-gray-400">Peminjaman Selesai:</span> {{ auth()->user()->borrows()->where('status', 'completed')->count() }}</p>
                                <p><span class="text-gray-500 dark:text-gray-400">Total Feedback:</span> {{ auth()->user()->feedback()->count() }}</p>
                                <p><span class="text-gray-500 dark:text-gray-400">Total Bookmark:</span> {{ auth()->user()->bookmarks()->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6">
                        <h4 class="font-semibold mb-2">Riwayat Peminjaman Terakhir</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-100 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal Pinjam</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse(auth()->user()->borrows()->latest()->take(5)->get() as $borrow)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $borrow->item->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $borrow->created_at->format('d F Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($borrow->status === 'active') bg-green-100 text-green-800
                                                    @elseif($borrow->status === 'completed') bg-blue-100 text-blue-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ ucfirst($borrow->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                                Belum ada riwayat peminjaman
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <a href="{{ route('profile.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 