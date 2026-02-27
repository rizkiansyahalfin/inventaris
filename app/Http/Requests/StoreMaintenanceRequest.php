<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id' => 'required|exists:items,id',
            'type' => 'required|string|in:Perawatan,Perbaikan,Penggantian',
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'completion_date' => 'nullable|date|after_or_equal:start_date',
            'update_condition' => 'nullable|string|in:Baik,Rusak Ringan,Rusak Berat',
            'update_item_status' => 'nullable|string|in:Tersedia,Perlu Servis,Rusak,Perlu Ganti',
        ];
    }
}
