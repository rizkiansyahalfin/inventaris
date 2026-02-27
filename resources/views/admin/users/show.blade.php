@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center">
                        <div
                            class="h-16 w-16 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-2xl font-bold text-indigo-700 dark:text-indigo-300">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="ml-4">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.users.index') }}"
                            class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Kembali
                        </a>
                        <a href="{{ route('admin.users.edit', $user) }}"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors">
                            Edit Profil
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t dark:border-gray-700 pt-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Informasi Akun</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : ($user->role === 'petugas' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                        {{ $user->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Terdaftar Pada</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                    {{ $user->created_at->format('d F Y, H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Update Terakhir</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                                    {{ $user->updated_at->format('d F Y, H:i') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Pengaturan Cepat</h3>
                        <div class="space-y-4">
                            <form action="{{ route('admin.users.update-role', $user) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <label for="role_select" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ubah
                                    Role</label>
                                <div class="mt-1 flex space-x-2">
                                    <select name="role" id="role_select"
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                        <option value="petugas" {{ $user->role === 'petugas' ? 'selected' : '' }}>Petugas
                                        </option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    <button type="submit"
                                        class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                        Update
                                    </button>
                                </div>
                            </form>

                            <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ubah Status</label>
                                <button type="submit"
                                    class="mt-1 w-full px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $user->status === 'active' ? 'bg-red-50 text-red-700 hover:bg-red-100 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50' : 'bg-green-50 text-green-700 hover:bg-green-100 dark:bg-green-900/30 dark:text-green-300 dark:hover:bg-green-900/50' }}">
                                    {{ $user->status === 'active' ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                                </button>
                            </form>

                            <div>
                                <a href="{{ route('admin.users.reset-password.form', $user) }}"
                                    class="block w-full text-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    Reset Password
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($user->activityLogs && $user->activityLogs->count() > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Aktivitas Terakhir</h3>
                    <div class="space-y-4">
                        @foreach($user->activityLogs->take(5) as $log)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 pt-0.5">
                                    <div class="h-2 w-2 rounded-full bg-indigo-500 mt-1.5"></div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-900 dark:text-gray-200">{{ $log->description }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $log->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
