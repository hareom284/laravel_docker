<?php

namespace Src\Company\Security\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorepermissionRequest  extends FormRequest
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
                'unique:permissions,name',
                'required',
            ],
        ];
    }
}
