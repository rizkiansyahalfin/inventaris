@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen py-12">
    <h1 class="text-6xl font-bold text-red-600 mb-4">403</h1>
    <h2 class="text-2xl font-semibold mb-2">Akses Ditolak</h2>
    <p class="mb-6 text-gray-600">Anda tidak memiliki hak akses untuk halaman ini.</p>
    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Kembali ke Dashboard</a>
</div>
@endsection 