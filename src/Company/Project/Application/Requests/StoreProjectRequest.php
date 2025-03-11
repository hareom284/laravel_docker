<?php

namespace Src\Company\Project\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'customer_id' => [
                'required'
            ],
            'company_id' => [
                'required'
            ],
            'type' => [
                'required'
            ],
            'description' => [
                'nullable'
            ],
            // 'attachment_file' => [
            //     'nullable',
            //     'file'
            // ],
            // 'attachment_title' => [
            //     'nullable'
            // ]
            // 'invoice_no' => [
            //     'required'
            // ],
           
            // 'collection_of_keys' => [
            //     'required'
            // ],
            // 'expected_date_of_completion' => [
            //     'required'
            // ],
            // 'completed_date' => ['required'],
            // 'project_status' => [
            //     'required'
            // ],
            
            // 'street_name' => [
            //     'required'
            // ],
            // 'block_num' => [
            //     'required'
            // ],
            // 'unit_num' => [
            //     'required'
            // ],
            // 'postal_code' => [
            //     'required'
            // ],
            // 'salesperson_ids' => [
            //     'required'
            // ],
            
        ];
    }
}