<?php

namespace Src\Company\Security\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateUserPasswordRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'currentpassword' => [
                'min:8',
                'required',
            ],
            'updatedpassword' => [
                'required',
                'min:8'
            ]
        ];
    }
}
