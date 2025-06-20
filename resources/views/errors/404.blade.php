@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen py-12">
    <h1 class="text-6xl font-bold text-yellow-500 mb-4">404</h1>
    <h2 class="text-2xl font-semibold mb-2">Halaman Tidak Ditemukan</h2>
    <p class="mb-6 text-gray-600">Maaf, halaman yang Anda cari tidak tersedia.</p>
    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Kembali ke Dashboard</a>
</div>
@endsection 