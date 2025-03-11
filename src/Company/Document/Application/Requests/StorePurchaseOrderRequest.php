<?php

namespace Src\Company\Document\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'project_id' => [
                'required',
                'exists:projects,id'
            ],
            'vendor_id' => [
                'required',
                'exists:vendors,id'
            ],
            'attn' => [
                'required'
            ],
            'date' => [
                'required'
            ],
            'time' => [
                'nullable'
            ],
            'pages' => [
                'nullable'
            ],
            'sales_rep_signature' => [
                'required'
            ],
            'remark' => [
                'nullable'
            ],
            'delivery_date' => [
                'required'
            ],
            'delivery_time_of_the_day' => [
                'required'
            ],
            'items' => [
                'required'
            ]
        ];
    }
}