@extends('layouts.guest')

@section('content')
<div class="w-full">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-900 dark:text-white">Login</h2>
    
    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 p-3 rounded-md border border-green-200 dark:border-green-800">
            {{ session('status') }}
        </div>
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
        
        <div class="mb-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Ingat saya') }}</span>
            </label>
        </div>
        
        <div class="mb-6">
            <button type="submit" style="background: linear-gradient(to right, #2563eb, #0891b2); color: white; width: 100%; padding: 12px 16px; font-weight: 600; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; border: none; cursor: pointer;" onmouseover="this.style.background='linear-gradient(to right, #1d4ed8, #0e7490)'" onmouseout="this.style.background='linear-gradient(to right, #2563eb, #0891b2)'">Masuk</button>
        </div>
        
        @if (Route::has('password.request'))
            <div class="text-center mb-6">
                <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline dark:text-blue-400 text-sm">
                    {{ __('Lupa kata sandi?') }}
                </a>
            </div>
        @endif
    </form>
    
    <div class="text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline dark:text-blue-400 font-medium">Daftar</a>
        </p>
    </div>
</div>
@endsection