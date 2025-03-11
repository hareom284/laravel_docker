<?php

namespace Src\Company\UserManagement\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest  extends FormRequest
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
                'email',
                'unique:users'
            ],
            'password' => [
                'required'
            ],
            'password_confirmation' => [
                'required'
            ],
            'contact_no' => [
                'required',
                // 'integer',
                'digits:8'
            ],
            'profile_pic' => [
                'nullable'
            ],
            'rank_id' => [
                'nullable',
                'exists:ranks,id'
            ],
            'role_ids' => [
                'required'
            ]
        ];
    }
}
