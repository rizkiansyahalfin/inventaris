@extends('layouts.guest')
@section('content')
    <div class="w-full">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-900 dark:text-white">Reset Kata Sandi</h2>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="mb-4">
                <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Email</label>
                <input id="email" name="email" type="email" required autofocus
                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    value="{{ old('email') }}">
                @if($errors->has('email'))
                    <div class="mt-2 text-sm text-red-600">{{ $errors->first('email') }}</div>
                @endif
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Kata Sandi
                    Baru</label>
                <input id="password" name="password" type="password" required
                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                @if($errors->has('password'))
                    <div class="mt-2 text-sm text-red-600">{{ $errors->first('password') }}</div>
                @endif
            </div>
            <div class="mb-6">
                <label for="password_confirmation"
                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Konfirmasi Kata Sandi</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                @if($errors->has('password_confirmation'))
                    <div class="mt-2 text-sm text-red-600">{{ $errors->first('password_confirmation') }}</div>
                @endif
            </div>
            <button type="submit"
                class="w-full justify-center py-3 px-4 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition">Reset
                Kata Sandi</button>
        </form>
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline dark:text-blue-400">Kembali ke Login</a>
        </div>
    </div>
@endsection