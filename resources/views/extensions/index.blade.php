<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Permintaan Perpanjangan') }}
        </h2>
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

                    @if ($extensions->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500">Belum ada permintaan perpanjangan yang dibuat</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border">
                                <thead>
                                    <tr>
                                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Peminjaman</th>
                                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Kembali Awal</th>
                                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Kembali Baru</th>
                                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan</th>
                                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($extensions as $extension)
                                        <tr>
                                            <td class="py-4 px-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        @if ($extension->borrow->item->image)
                                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $extension->borrow->item->image) }}" alt="{{ $extension->borrow->item->name }}">
                                                        @else
                                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $extension->borrow->item->name }}</div>
                                                        <div class="text-sm text-gray-500">Kode: {{ $extension->borrow->item->code ?? '-' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $extension->borrow->borrow_date->format('d M Y') }}
                                            </td>
                                            <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $extension->original_return_date->format('d M Y') }}
                                            </td>
                                            <td class="py-4 px-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $extension->new_return_date->format('d M Y') }}
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="text-sm text-gray-500 max-w-xs truncate">{{ $extension->reason }}</div>
                                            </td>
                                            <td class="py-4 px-4 whitespace-nowrap">
                                                @if ($extension->status === 'pending')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Menunggu
                                                    </span>
                                                @elseif ($extension->status === 'approved')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Disetujui
                                                    </span>
                                                @elseif ($extension->status === 'rejected')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Ditolak
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('extensions.show', $extension) }}" class="text-blue-600 hover:text-blue-900">Lihat Detail</a>
                                                    
                                                    @if (auth()->user()->hasRole('petugas') || auth()->user()->hasRole('admin'))
                                                        @if ($extension->status === 'pending')
                                                            <form action="{{ route('extensions.update_status', $extension) }}" method="POST" class="inline">
                                                                @csrf
                                                                <input type="hidden" name="status" value="approved">
                                                                <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Apakah Anda yakin ingin menyetujui perpanjangan ini?')">
                                                                    Setujui
                                                                </button>
                                                            </form>
                                                            
                                                            <form action="{{ route('extensions.update_status', $extension) }}" method="POST" class="inline">
                                                                @csrf
                                                                <input type="hidden" name="status" value="rejected">
                                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menolak perpanjangan ini?')">
                                                                    Tolak
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6">
                            {{ $extensions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 