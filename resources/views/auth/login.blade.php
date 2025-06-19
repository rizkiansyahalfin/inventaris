@extends('layouts.guest')
@section('content')
<div class="w-full max-w-md mx-auto mt-10 bg-white dark:bg-gray-800 p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-900 dark:text-white">Login</h2>
    @if(session('status'))
        <div class="mb-4 text-green-600 dark:text-green-400">{{ session('status') }}</div>
    @endif
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Email</label>
            <input id="email" name="email" type="email" required autofocus class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="{{ old('email') }}">
            @if($errors->has('email'))
                <div class="mt-2 text-sm text-red-600">{{ $errors->first('email') }}</div>
            @endif
        </div>
        <div class="mb-4">
            <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Kata Sandi</label>
            <input id="password" name="password" type="password" required class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            @if($errors->has('password'))
                <div class="mt-2 text-sm text-red-600">{{ $errors->first('password') }}</div>
            @endif
        </div>
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50" name="remember">
                <label for="remember_me" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Ingat saya</label>
            </div>
            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline dark:text-blue-400">Lupa kata sandi?</a>
        </div>
        <button type="submit" class="w-full justify-center py-3 px-4 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition">Login</button>
    </form>
    <div class="mt-6 text-center">
        <a href="{{ route('register') }}" class="text-blue-600 hover:underline dark:text-blue-400">Belum punya akun? Daftar</a>
    </div>
</div>
@endsection