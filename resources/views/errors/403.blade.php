@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen py-12">
    <h1 class="text-6xl font-bold text-red-600 mb-4">403</h1>
    <h2 class="text-2xl font-semibold mb-2">Akses Ditolak</h2>
    <p class="mb-6 text-gray-600">Anda tidak memiliki hak akses untuk halaman ini.</p>
    <div class="flex flex-col items-center space-y-3">
        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 bg-gray-500 text-white rounded shadow hover:bg-gray-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6m-6 0v6m0 0H7m6 0h6" /></svg>
            Kembali ke Dashboard
        </a>
        <a href="#" onclick="window.history.back(); return false;" class="flex items-center px-4 py-2 bg-gray-500 text-white rounded shadow hover:bg-gray-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Kembali
        </a>
    </div>
</div>
@endsection 