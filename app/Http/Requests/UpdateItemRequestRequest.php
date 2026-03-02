<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        $itemRequest = $this->route('itemRequest');

        return $this->user()
            && $itemRequest->user_id === $this->user()->id
            && $itemRequest->isPending();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string',
        ];
    }
}
