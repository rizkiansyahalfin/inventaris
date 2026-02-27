@extends('layouts.guest')
@section('content')
    <div class="w-full">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-900 dark:text-white">Verifikasi Email</h2>
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-300">
            {{ __('Sebelum melanjutkan, silakan cek email Anda untuk link verifikasi.') }}
            {{ __('Jika Anda tidak menerima email tersebut, kami dapat mengirimkan lagi.') }}
        </div>
        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                {{ __('Link verifikasi baru telah dikirim ke email Anda.') }}
            </div>
        @endif
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                class="w-full justify-center py-3 px-4 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition">Kirim
                Ulang Email Verifikasi</button>
        </form>
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit"
                class="w-full justify-center py-3 px-4 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white font-semibold rounded-xl shadow hover:shadow-md transition">Logout</button>
        </form>
    </div>
@endsection