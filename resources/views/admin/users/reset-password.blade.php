@extends('layouts.app')

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
    <div class="p-6">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Reset Password</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Reset password untuk user {{ $user->name }}</p>
        </div>

        <form action="{{ route('admin.users.reset-password', $user) }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Password Baru -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password Baru</label>
                    <input type="password" name="password" id="password" required
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Konfirmasi Password Baru -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>

            <div class="mt-6 flex items-center space-x-3">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Reset Password
                </button>
                <a href="{{ route('admin.users.index') }}" class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection 