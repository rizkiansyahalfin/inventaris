@extends('layouts.app')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Feedback</h2>
@endsection
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            @if ($feedbacks->isEmpty())
                <div class="text-center py-8">
                    <p class="text-gray-500">Anda belum memberikan feedback untuk barang yang dipinjam</p>
                    @if(isset($borrowsTanpaFeedback) && $borrowsTanpaFeedback->count())
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold mb-4">Peminjaman yang Bisa Diberi Feedback</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border">
                                    <thead>
                                        <tr>
                                            <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                            <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Peminjaman</th>
                                            <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Kembali</th>
                                            <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($borrowsTanpaFeedback as $borrow)
                                            <tr>
                                                <td class="py-4 px-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            @if ($borrow->item->image)
                                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $borrow->item->image) }}" alt="{{ $borrow->item->name }}">
                                                            @else
                                                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                    </svg>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">{{ $borrow->item->name }}</div>
                                                            <div class="text-sm text-gray-500">Kode: {{ $borrow->item->code ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $borrow->borrow_date->format('d M Y') }}
                                                </td>
                                                <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $borrow->return_date ? $borrow->return_date->format('d M Y') : '-' }}
                                                </td>
                                                <td class="py-4 px-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('feedbacks.create', $borrow) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Beri Feedback</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('borrows.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            {{ __('Lihat Peminjaman Saya') }}
                        </a>
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border">
                        <thead>
                            <tr>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Peminjaman</th>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komentar</th>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Feedback</th>
                                <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($feedbacks as $feedback)
                                <tr>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if ($feedback->borrow->item->image)
                                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $feedback->borrow->item->image) }}" alt="{{ $feedback->borrow->item->name }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $feedback->borrow->item->name }}</div>
                                                <div class="text-sm text-gray-500">Kode: {{ $feedback->borrow->item->code ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $feedback->borrow->borrow_date->format('d M Y') }}
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $feedback->rating ? 'text-yellow-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endfor
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="max-w-xs truncate">{{ $feedback->comment }}</div>
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $feedback->created_at->format('d M Y') }}
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('feedbacks.show', $feedback) }}" class="text-blue-600 hover:text-blue-900">Lihat</a>
                                            <a href="{{ route('feedbacks.edit', $feedback) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $feedbacks->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 