<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,64}$/',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.string'   => 'Name must be a valid string.',
            'name.max'      => 'Name may not be longer than 255 characters.',
            'email.required' => 'Email is required.',
            'email.string'   => 'Email must be a valid string.',
            'email.email'    => 'Email format is invalid.',
            'email.max'      => 'Email may not be longer than 255 characters.',
            'email.unique'   => 'This email is already taken.',
            'password.required'  => 'Password is required.',
            'password.string'    => 'Password must be a valid string.',
            'password.confirmed' => 'Passwords do not match.',
            'password.regex'     => 'Password must be 8–64 characters and include uppercase, lowercase, number, and special character.',
        ];
    }
}
