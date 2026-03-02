@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6 flex-wrap gap-y-2">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Log Aktivitas</h1>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('activity-logs.export', request()->query()) }}"
                    class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">Export
                    Excel</a>
                <a href="{{ route('activity-logs.export-pdf', request()->query()) }}"
                    class="inline-block px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Export PDF</a>
                <a href="{{ route('activity-logs.export-csv', request()->query()) }}"
                    class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">Export
                    CSV</a>
            </div>
        </div>

        <form method="GET"
            class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 flex flex-wrap gap-4 items-end transition-colors">
            <div>
                <label class="block text-xs font-semibold mb-1 text-gray-700 dark:text-gray-300">Cari Deskripsi</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="border rounded px-2 py-1 w-full md:w-40 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="Kata kunci...">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-gray-700 dark:text-gray-300">Modul</label>
                <input type="text" name="model" value="{{ request('model') }}"
                    class="border rounded px-2 py-1 w-full md:w-28 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="Modul">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-gray-700 dark:text-gray-300">User</label>
                <select name="user_id"
                    class="border rounded px-2 py-1 w-full md:w-36 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">- Semua -</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-gray-700 dark:text-gray-300">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="border rounded px-2 py-1 w-full md:w-auto dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-gray-700 dark:text-gray-300">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="border rounded px-2 py-1 w-full md:w-auto dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <div class="flex flex-row gap-2 flex-none min-w-[180px]">
                <button type="submit"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">Filter</button>
                <a href="{{ route('activity-logs.index') }}"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">Reset</a>
            </div>
        </form>

        <div
            class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border dark:border-gray-700 transition-colors">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100 dark:bg-gray-900 transition-colors">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b dark:border-gray-700">
                                Waktu</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b dark:border-gray-700">
                                Pengguna</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b dark:border-gray-700">
                                Modul</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b dark:border-gray-700">
                                Deskripsi</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b dark:border-gray-700 min-w-[90px]">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($activityLogs as $log)
                            <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/30 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    <a href="{{ route('activity-logs.show', $log->id) }}"
                                        class="text-blue-600 hover:underline">{{ $log->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i:s') }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $log->user->name ?? 'Sistem' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    <span
                                        class="inline-block px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs font-semibold">{{ $log->module }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">
                                    {{ $log->description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <a href="{{ route('activity-logs.show', $log->id) }}"
                                        class="inline-block px-3 py-1 bg-blue-400 text-blue-900 font-bold rounded hover:bg-blue-500 text-xs shadow">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                    Tidak ada log aktivitas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4">
                {{ $activityLogs->links() }}
            </div>
        </div>
    </div>
@endsection