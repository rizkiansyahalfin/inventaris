<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isPetugas();
    }

    public function rules(): array
    {
        return [
            'report_date' => 'required|date',
            'activities' => 'required|string',
            'challenges' => 'nullable|string',
            'hours_worked' => 'required|numeric|min:0.5|max:24',
            'status' => 'required|in:draft,submitted',
        ];
    }
}
