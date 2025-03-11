<?php

namespace Src\Company\UserManagement\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'unique:roles',
            ],
            'description' => [
                'nullable',
                'min:8'
            ]
        ];
    }
}
