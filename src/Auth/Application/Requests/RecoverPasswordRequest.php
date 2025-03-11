<?php

namespace Src\Auth\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecoverPasswordRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => [
                'required',
                'email',
                'exists:users,email'
            ]
        ];
    }
}
