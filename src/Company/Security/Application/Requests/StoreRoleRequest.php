<?php

namespace Src\Company\Security\Application\Requests;

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
                'string',
                'required',
            ],
        ];
    }
}
