@extends('layouts.app')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Permintaan Barang</h2>
@endsection
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            @if (session('status'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            @if ($itemRequests->isEmpty())
                <div class="text-center py-8">
                    <p class="text-gray-500">Belum ada permintaan barang yang dibuat</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border">
                        <thead>
                            <tr>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan</th>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($itemRequests as $request)
                                <tr>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $request->name }}</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="text-sm text-gray-500 max-w-xs truncate">{{ $request->description }}</div>
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $request->quantity }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="text-sm text-gray-500 max-w-xs truncate">{{ $request->reason }}</div>
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @if ($request->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Menunggu
                                            </span>
                                        @elseif ($request->status === 'approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Disetujui
                                            </span>
                                        @elseif ($request->status === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Ditolak
                                            </span>
                                        @elseif ($request->status === 'fulfilled')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Terpenuhi
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $request->created_at->format('d M Y') }}
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('item-requests.show', $request) }}" class="text-blue-600 hover:text-blue-900">Lihat</a>
                                            
                                            @if ($request->status === 'pending')
                                            <a href="{{ route('item-requests.edit', $request) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                            
                                            <button
                                                onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin menghapus permintaan ini?')) document.getElementById('delete-form-{{ $request->id }}').submit();"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Hapus
                                            </button>
                                            <form id="delete-form-{{ $request->id }}" action="{{ route('item-requests.destroy', $request) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $itemRequests->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 