<?php

namespace Src\Auth\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'current_password' => [
                'required',
                'current_password:api'
            ],
            'new_password' => [
                'required',
                'string',
                'min:8', // Minimum length
                'confirmed', // Ensure the new password matches the confirmation
            ]
        ];
    }

    public function messages()
    {
        return [
            'current_password.required' => 'Current password is required.',
            'current_password.current_password' => 'Current password is incorrect.',
            'new_password.required' => 'New password is required.',
            'new_password.string' => 'New password must be a string.',
            'new_password.min' => 'New password must be at least 8 characters long.',
            'new_password.confirmed' => 'New password confirmation does not match.',
            'new_password_confirmation.required' => 'New password confirmation is required.',
        ];
    }
}

