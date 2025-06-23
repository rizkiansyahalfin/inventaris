@extends('layouts.guest')

@section('content')
<div class="w-full">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-900 dark:text-white">Lupa Kata Sandi</h2>
    
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 text-center">
        Masukkan alamat email Anda dan kami akan mengirimkan link untuk mereset kata sandi.
    </p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 p-3 rounded-md border border-green-200 dark:border-green-800">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        
        <div class="mb-6">
            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Email</label>
            <input id="email" name="email" type="email" required autofocus class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="{{ old('email') }}" placeholder="nama@perusahaan.com">
            @if($errors->has('email'))
                <div class="mt-2 text-sm text-red-600">{{ $errors->first('email') }}</div>
            @endif
        </div>
        
        <div class="mb-6">
            <button type="submit" style="background: linear-gradient(to right, #2563eb, #0891b2); color: white; width: 100%; padding: 12px 16px; font-weight: 600; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; border: none; cursor: pointer;" onmouseover="this.style.background='linear-gradient(to right, #1d4ed8, #0e7490)'" onmouseout="this.style.background='linear-gradient(to right, #2563eb, #0891b2)'">Kirim Link Reset Password</button>
        </div>
    </form>
    
    <div class="text-center mb-6">
        <a href="{{ route('login') }}" class="text-blue-600 hover:underline dark:text-blue-400 text-sm">
            Kembali ke Login
        </a>
    </div>
    
    <!-- Help Text -->
    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
        <div class="text-sm text-blue-700 dark:text-blue-300">
            <p class="font-medium mb-2">Bantuan Reset Password:</p>
            <ul class="text-xs space-y-1 text-blue-600 dark:text-blue-400">
                <li>• Periksa folder spam/junk email Anda</li>
                <li>• Link reset berlaku selama 60 menit</li>
                <li>• Hubungi admin jika tidak menerima email</li>
            </ul>
        </div>
    </div>
</div>
@endsection