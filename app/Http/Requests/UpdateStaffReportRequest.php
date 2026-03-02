<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $staffReport = $this->route('staffReport');

        return $this->user()
            && $staffReport->user_id === $this->user()->id
            && $staffReport->status === 'draft';
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
