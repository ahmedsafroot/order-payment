<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
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
           'payment_method'  => 'required|string|in:credit_card,paypal',
           'details' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'Payment method is required.',
            'payment_method.string'   => 'Payment method must be a valid string.',
            'payment_method.in'       => 'Unsupported payment method. Allowed: credit_card, paypal.',
            'details.array'   => 'Payment details must be an array.',
        ];
    }
}


