<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'description' => ['required', 'string', 'max:1000'],
            'category' => ['required', 'string', 'max:255'],
            'merchant' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', 'string', 'max:255'],
            'expense_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.max' => 'The amount must not exceed 999,999,999.99.',
            'expense_date.before_or_equal' => 'The expense date cannot be in the future.',
        ];
    }
}
