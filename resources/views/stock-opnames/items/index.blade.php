@extends('layouts.app')

@section('content')
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Daftar Item Stock Opname</h2>
        @if(session('success'))
            <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 mb-4"
                role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Nama Barang</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Jumlah Seharusnya</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Jumlah Aktual</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Kondisi</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                {{ optional($item->item)->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                {{ $item->expected_quantity ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                {{ $item->actual_quantity ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                {{ $item->condition ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item && $stockOpname)
                                    <a href="{{ route('stock-opnames.items.check', [$stockOpname, $item]) }}"
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">Periksa</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Tidak ada item untuk
                                stock opname ini.</td>
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