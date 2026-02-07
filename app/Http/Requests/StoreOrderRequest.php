<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:1|regex:/^\d{1,12}(\.\d{1,3})?$/',
        ];
    }


    public function messages(): array
    {
        return [
            'items.required' => 'You need to add at least one item.',
            'items.array'    => 'Items must be sent as a list.',
            'items.min'      => 'You need to add at least one item.',

            'items.*.product_name.required' => 'Please enter the product name.',
            'items.*.product_name.string'   => 'The product name must be text.',
            'items.*.product_name.max'      => 'The product name can’t be longer than :max characters.',

            'items.*.quantity.required' => 'Please enter the quantity.',
            'items.*.quantity.integer'  => 'Quantity must be a whole number.',
            'items.*.quantity.min'      => 'Quantity must be at least :min.',

            'items.*.price.required' => 'Please enter the price.',
            'items.*.price.numeric'  => 'Price must be a number.',
            'items.*.price.min'      => 'Price must be at least :min.',
            'items.*.price.regex'    => 'Please enter a valid price (up to 12 digits and 3 decimals).',
        ];
    }

}
