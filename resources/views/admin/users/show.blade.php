@extends('layouts.app')

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Detail User</h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Edit
                </a>
                <a href="{{ route('admin.users.reset-password.form', $user) }}" class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700">
                    Reset Password
                </a>
                @if(auth()->id() !== $user->id)
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                            Hapus
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">Informasi User</h3>
                
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</p>
                        <p class="text-gray-800 dark:text-gray-200">{{ $user->id }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama</p>
                        <p class="text-gray-800 dark:text-gray-200">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</p>
                        <p class="text-gray-800 dark:text-gray-200">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</p>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                            {{ $user->role === 'petugas' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                            {{ $user->role === 'user' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Dibuat</p>
                        <p class="text-gray-800 dark:text-gray-200">{{ $user->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Terakhir Update</p>
                        <p class="text-gray-800 dark:text-gray-200">{{ $user->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>

            @if($user->role !== 'admin')
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">Pengaturan Role</h3>
                
                @if(auth()->id() !== $user->id)
                <form action="{{ route('admin.users.update-role', $user) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-4">
                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                        <select name="role" id="role" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="petugas" {{ $user->role == 'petugas' ? 'selected' : '' }}>Petugas</option>
                            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Perbarui Role
                    </button>
                </form>
                @else
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900 rounded-md">
                    <p class="text-yellow-800 dark:text-yellow-200">
                        Anda tidak dapat mengubah role Anda sendiri.
                    </p>
                </div>
                @endif
            </div>
            @endif
        </div>

        <div class="mt-6">
            <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                &larr; Kembali ke daftar user
            </a>
        </div>
    </div>
</div>
@endsection 