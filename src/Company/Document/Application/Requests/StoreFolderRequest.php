<?php

namespace Src\Company\Document\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFolderRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => [
                'required',
                // 'unique:folders',
            ],
            'allow_customer_view' => [
                'required',
                'boolean'
            ],
            'project_id' => [
                'required',
                'exists:projects,id'
            ]
        ];
    }
}
