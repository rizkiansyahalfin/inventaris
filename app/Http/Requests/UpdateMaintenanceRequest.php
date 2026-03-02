<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'completion_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
            'update_condition' => 'nullable|string|in:Baik,Rusak Ringan,Rusak Berat',
        ];
    }
}
