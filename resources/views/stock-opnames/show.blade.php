@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail Stock Opname</h1>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ $stockOpname->name }}</h5>
            <p class="card-text"><strong>Tanggal Mulai:</strong> {{ $stockOpname->start_date }}</p>
            <p class="card-text"><strong>Tanggal Selesai:</strong> {{ $stockOpname->end_date }}</p>
            <p class="card-text"><strong>Status:</strong> {{ $stockOpname->status }}</p>
            <p class="card-text"><strong>Catatan:</strong> {{ $stockOpname->notes }}</p>
        </div>
    </div>
    <a href="{{ route('stock-opnames.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection 