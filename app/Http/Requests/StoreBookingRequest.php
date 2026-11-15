<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
     * Question 3(b)
     */
    public function rules(): array
    {
        return [
            'service_id' => ['required', 'exists:services,id'],
            'estimated_kg' => ['required', 'numeric', 'min:5'],
            'pickup_address' => ['required', 'string', 'max:255'],
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'branch_id' => ['nullable', 'exists:branches,id'],
        ];
    }
}
