<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        $borrow = $this->route('borrow');

        return $this->user()
            && $borrow->user_id === $this->user()->id
            && $borrow->canSubmitFeedback();
    }

    public function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ];
    }
}
