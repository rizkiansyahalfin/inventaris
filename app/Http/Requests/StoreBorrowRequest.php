<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBorrowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'item_id' => [
                'required',
                'exists:items,id',
                function ($attribute, $value, $fail) {
                    $item = \App\Models\Item::find($value);
                    if (!$item)
                        return;
                    if ($item->status !== \App\Models\Item::STATUS_AVAILABLE) {
                        $fail('Barang ini tidak tersedia untuk dipinjam.');
                    }
                    if ($item->condition === 'Rusak Berat') {
                        $fail('Barang dengan kondisi "Rusak Berat" tidak dapat dipinjam.');
                    }
                    if ($item->stock < 1) {
                        $fail('Stok barang tidak mencukupi.');
                    }
                }
            ],
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:borrow_date',
            'notes' => 'nullable|string',
        ];
    }
}
