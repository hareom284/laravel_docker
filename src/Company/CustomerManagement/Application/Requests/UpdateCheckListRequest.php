<?php

namespace Src\Company\CustomerManagement\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCheckListRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'checklist_template_item_id' => [
                'required',
                'exists:checklist_template_items,id'
            ],
            'customer_id' => [
                'required',
                'exists:customers,id'
            ],
            'status' => [
                'required',
                'boolean'
            ]
        ];
    }
}
