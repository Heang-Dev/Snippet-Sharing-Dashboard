<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

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
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-zA-Z0-9_-]+$/',
                'unique:users',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users',
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'device_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Username is required.',
            'username.min' => 'Username must be at least 3 characters.',
            'username.max' => 'Username must not exceed 30 characters.',
            'username.regex' => 'Username can only contain letters, numbers, underscores, and hyphens.',
            'username.unique' => 'This username is already taken.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
