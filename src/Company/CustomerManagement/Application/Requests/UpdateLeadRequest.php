<?php

namespace Src\Company\CustomerManagement\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        $userId = $this->route('id');

        return [
            'name_prefix' => [
                'nullable'
            ],
            'first_name' => [
                'required'
            ],
            'last_name' => [
                'nullable'
            ],
            'email' => [
                'nullable',
                // 'unique:users'
                Rule::unique('users')->ignore($userId),
            ],
            'contact_no' => [
                'required',
                // 'unique:users'
                Rule::unique('users')->ignore($userId),
            ],
            'nric' => [
                'nullable'
            ],
            'source' => [
                'nullable'
            ],
            'additional_information' => [
                'nullable'
            ],
            'profile_pic' => [
                'nullable',
            ],
            'customer_attachment' => [
                'nullable',
                'file',
                'mimes:jpg,png,pdf',
                'max:10240',
            ]
            // 'type' => [
            //     'nullable'
            // ],
            // 'street_name' => [
            //     'nullable'
            // ],
            // 'block_num' => [
            //     'nullable'
            // ],
            // 'unit_num' => [
            //     'nullable'
            // ],
            // 'postal_code' => [
            //     'nullable'
            // ],
            // 'collection_of_keys' => [
            //     'nullable'
            // ],
            // 'expected_date_of_completion' => [
            //     'nullable'
            // ],
            // 'invoice_no' => [
            //     'nullable'
            // ],
            // 'project_add_info' => [
            //     'nullable'
            // ],
            // 'company_id' => [
            //     'nullable'
            // ]
        ];
    }

    public function messages()
    {
        return [
            'email.required_if' => 'Email field is required.',
            'customer_attachment.max' => 'File must not be greater than 10 MB.',
        ];
    }
}
