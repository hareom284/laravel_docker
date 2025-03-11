<?php

namespace Src\Company\Document\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequirementRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => [
                // 'required',
                'unique:project_requirements',
            ],
            'document_file' => [
                'required',
                // 'file'
            ],
            'project_id' => [
                'required',
                'exists:projects,id'
            ]
        ];
    }
}
