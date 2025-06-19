@extends('layouts.app')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Stock Opname</h2>
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

            @if ($stockOpnames->isEmpty())
                <div class="text-center py-8">
                    <p class="text-gray-500">Belum ada sesi stock opname yang dibuat</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border">
                        <thead>
                            <tr>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($stockOpnames as $opname)
                                <tr>
                                    <td class="py-4 px-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $opname->code }}
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $opname->name }}
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @if ($opname->status === 'draft')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Draft
                                            </span>
                                        @elseif ($opname->status === 'in_progress')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Sedang Berjalan
                                            </span>
                                        @elseif ($opname->status === 'completed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Selesai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $opname->user->name }}
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $opname->started_at ? $opname->started_at->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $opname->completed_at ? $opname->completed_at->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $opname->progress }}%"></div>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1 text-right">{{ $opname->progress }}%</div>
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('stock-opnames.show', $opname) }}" class="text-blue-600 hover:text-blue-900">Lihat</a>
                                            
                                            @if ($opname->status === 'draft')
                                                <a href="{{ route('stock-opnames.edit', $opname) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                                
                                                <form action="{{ route('stock-opnames.start', $opname) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-purple-600 hover:text-purple-900">
                                                        Mulai
                                                    </button>
                                                </form>
                                                
                                                <button
                                                    onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin menghapus sesi stock opname ini?')) document.getElementById('delete-form-{{ $opname->id }}').submit();"
                                                    class="text-red-600 hover:text-red-900"
                                                >
                                                    Hapus
                                                </button>
                                                <form id="delete-form-{{ $opname->id }}" action="{{ route('stock-opnames.destroy', $opname) }}" method="POST" class="hidden">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            @elseif ($opname->status === 'in_progress')
                                                <a href="{{ route('stock-opnames.items.index', $opname) }}" class="text-indigo-600 hover:text-indigo-900">Periksa Barang</a>
                                                
                                                <form action="{{ route('stock-opnames.complete', $opname) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan stock opname ini?')">
                                                        Selesai
                                                    </button>
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
                    {{ $stockOpnames->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 