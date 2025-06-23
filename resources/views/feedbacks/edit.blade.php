@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Feedback</h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Edit Feedback untuk Barang</h3>
            <div class="mb-6 flex items-center">
                <div class="flex-shrink-0 h-14 w-14">
                    @if ($feedback->borrow->item->image)
                        <img class="h-14 w-14 rounded-full object-cover" src="{{ asset('storage/' . $feedback->borrow->item->image) }}" alt="{{ $feedback->borrow->item->name }}">
                    @else
                        <div class="h-14 w-14 rounded-full bg-gray-200 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="ml-4">
                    <div class="text-base font-medium text-gray-900">{{ $feedback->borrow->item->name }}</div>
                    <div class="text-sm text-gray-500">Kode: {{ $feedback->borrow->item->code ?? '-' }}</div>
                    <div class="text-sm text-gray-500">Tanggal Peminjaman: {{ $feedback->borrow->borrow_date->format('d M Y') }}</div>
                    <div class="text-sm text-gray-500">Tanggal Kembali: {{ $feedback->borrow->return_date ? $feedback->borrow->return_date->format('d M Y') : '-' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('feedbacks.update', $feedback) }}">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label for="rating" class="block text-sm font-medium text-gray-700">Rating <span class="text-red-500">*</span></label>
                    <div class="flex items-center mt-2 space-x-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <label>
                                <input type="radio" name="rating" value="{{ $i }}" class="hidden" @if(old('rating', $feedback->rating) == $i) checked @endif required>
                                <svg class="w-8 h-8 cursor-pointer js-rating-star {{ old('rating', $feedback->rating) >= $i ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </label>
                        @endfor
                    </div>
                    @error('rating')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="comment" class="block text-sm font-medium text-gray-700">Komentar</label>
                    <textarea id="comment" name="comment" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('comment', $feedback->comment) }}</textarea>
                    @error('comment')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-row-reverse justify-end gap-2">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Update Feedback</button>
                    <a href="{{ route('feedbacks.show', $feedback) }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const radios = document.querySelectorAll('input[name="rating"]');
    const stars = document.querySelectorAll('.js-rating-star');
    function updateStars(rating) {
        stars.forEach((star, idx) => {
            if (idx < rating) {
                star.classList.add('text-yellow-400');
                star.classList.remove('text-gray-300');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }
    radios.forEach(radio => {
        radio.addEventListener('change', function () {
            updateStars(parseInt(this.value));
        });
    });
    // Inisialisasi awal
    const checked = document.querySelector('input[name="rating"]:checked');
    if (checked) updateStars(parseInt(checked.value));
});
</script>
@endsection 