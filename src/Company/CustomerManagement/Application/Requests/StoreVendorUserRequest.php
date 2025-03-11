<?php

namespace Src\Company\CustomerManagement\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorUserRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name_prefix' => [
                'nullable',
            ],
            'first_name' => [
                'required'
            ],
            'last_name' => [
                'nullable',
            ],
            'email' => [
                'nullable',
                'unique:users'
            ],
            'contact_no' => [
                'required',
                'unique:users'
            ],
            'nric' => [
                'nullable',
            ],
            'source' => [
                'nullable',
            ],
            'profile_pic' => [
                'nullable',
            ],
        ];

        return $rules;
    }

}
