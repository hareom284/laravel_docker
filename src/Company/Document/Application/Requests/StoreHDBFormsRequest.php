<?php

namespace Src\Company\Document\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHDBFormsRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'required'
            ],
            'document_file' => [
                'required',
                'file',
                'max:100000'
            ],
            'project_id' => [
                'required',
                'exists:projects,id'
            ]
        ];
    }
}
