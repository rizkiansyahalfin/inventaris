@extends('layouts.guest')

@section('content')
<div class="w-full">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-900 dark:text-white">Daftar</h2>
    
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 text-center">
        Bergabung dengan Sistem Inventaris
    </p>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        
        <div class="mb-4">
            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nama Lengkap</label>
            <input id="name" name="name" type="text" required autofocus class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="{{ old('name') }}" placeholder="Masukkan nama lengkap">
            @if($errors->has('name'))
                <div class="mt-2 text-sm text-red-600">{{ $errors->first('name') }}</div>
            @endif
        </div>
        
        <div class="mb-4">
            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Email</label>
            <input id="email" name="email" type="email" required class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="{{ old('email') }}" placeholder="nama@perusahaan.com">
            @if($errors->has('email'))
                <div class="mt-2 text-sm text-red-600">{{ $errors->first('email') }}</div>
            @endif
        </div>
        
        <div class="mb-4">
            <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Kata Sandi</label>
            <input id="password" name="password" type="password" required class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Minimal 8 karakter">
            @if($errors->has('password'))
                <div class="mt-2 text-sm text-red-600">{{ $errors->first('password') }}</div>
            @endif
        </div>
        
        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Konfirmasi Kata Sandi</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Ulangi kata sandi">
            @if($errors->has('password_confirmation'))
                <div class="mt-2 text-sm text-red-600">{{ $errors->first('password_confirmation') }}</div>
            @endif
        </div>
        
        <div class="mb-6">
            <button type="submit" style="background: linear-gradient(to right, #2563eb, #0891b2); color: white; width: 100%; padding: 12px 16px; font-weight: 600; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; border: none; cursor: pointer;" onmouseover="this.style.background='linear-gradient(to right, #1d4ed8, #0e7490)'" onmouseout="this.style.background='linear-gradient(to right, #2563eb, #0891b2)'">Daftar</button>
        </div>
    </form>
    
    <div class="text-center mb-6">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline dark:text-blue-400 font-medium">Login</a>
        </p>
    </div>
    
    <!-- Password Requirements -->
    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg mb-4">
        <div class="text-sm text-blue-700 dark:text-blue-300">
            <p class="font-medium mb-2">Persyaratan Kata Sandi:</p>
            <ul class="text-xs space-y-1 text-blue-600 dark:text-blue-400">
                <li>• Minimal 8 karakter</li>
                <li>• Kombinasi huruf dan angka</li>
                <li>• Hindari informasi pribadi</li>
            </ul>
        </div>
    </div>
    
    <!-- Terms Notice -->
    <div class="p-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg">
        <p class="text-xs text-gray-600 dark:text-gray-400 text-center">
            Dengan mendaftar, Anda menyetujui 
            <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Syarat & Ketentuan</a> 
            dan 
            <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Kebijakan Privasi</a> 
            sistem inventaris.
        </p>
    </div>
</div>
@endsection