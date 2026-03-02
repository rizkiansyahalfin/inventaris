<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:1',
            'condition' => 'required|string|in:Baik,Rusak Ringan,Rusak Berat',
            'status' => 'required|string|in:Tersedia,Dipinjam,Dalam Perbaikan,Rusak,Hilang',
            'location_id' => 'nullable|exists:locations,id',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
