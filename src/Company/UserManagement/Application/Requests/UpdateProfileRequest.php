<?php

namespace Src\Company\UserManagement\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => [
                'required'
            ],
            'last_name' => [
                'required'
            ],
            'name_prefix' => [
                'required'
            ],
            'email' => [
                'required',
                'email'
            ],
            'contact_no' => [
                'required',
                // 'integer',
                'digits:8'
            ],
            'profile_pic' => [
                'nullable'
            ],
            'password' => [
                'nullable',
                'min:8'
            ],
            'password_confirmation' => [
                'nullable',
                'min:8'
            ]
        ];
    }
}
