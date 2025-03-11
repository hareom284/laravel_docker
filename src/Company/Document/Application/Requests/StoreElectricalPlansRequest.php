<?php

namespace Src\Company\Document\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreElectricalPlansRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'document_file' => [
                'required',
                'file'
            ],
            'salesperson_id' => [
                'required',
                'exists:staffs,id'
            ],
            'project_id' => [
                'required',
                'exists:projects,id'
            ]
        ];
    }
}
