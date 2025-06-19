@extends('layouts.app')

@section('content')
<div class="p-6">
    <h2 class="text-xl font-semibold mb-4">Daftar Item Stock Opname</h2>
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Seharusnya</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Aktual</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($items as $item)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ optional($item->item)->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->expected_quantity ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->actual_quantity ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->condition ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($item && $stockOpname)
                            <a href="{{ route('stock-opnames.items.check', [$stockOpname, $item]) }}" class="text-blue-600 hover:text-blue-900">Periksa</a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada item untuk stock opname ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>
@endsection 