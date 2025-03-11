<?php

namespace Src\Company\CustomerManagement\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLeadRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        $loginUser = auth('sanctum')->user();
        $salepersonRequire = false;

        if ($loginUser->roles->contains('name', 'Management')) {
            $salepersonRequire = true;
        } else {
            $salepersonRequire = false;
        }

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
            'customer_attachment' => [
                'nullable',
                'file',
                'mimes:jpg,png,pdf',
                'max:10240',
            ]
            // 'project_name' => [
            //     'required_if:pselect,1'
            // ],
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

        if ($salepersonRequire) {
            $rules['saleperson_ids'] = 'required';
        }

        return $rules;

    }

    public function messages()
    {
        return [
            // 'project_name' => 'The project name is required.',
            'type' => 'The property type is required.',
            'street_name' => 'The street name is required.',
            'block_num' => 'The block number is required',
            'unit_num' => 'The unit number is required.',
            'postal_code' => 'The postal code is required.',
            'collection_of_keys' => 'The collection of keys is required.',
            'expected_date_of_completion' => 'The expected completion date is required.',
            'invoice_no' => 'The invoice no is required.',
            'project_add_info' => 'The project additional info is required.',
            'company_id' => 'The company is required.',
            'saleperson_ids' => 'Need to chooose at least one saleperson.',
            'customer_attachment.max' => 'File must not be greater than 10 MB.',
        ];
    }

}
