<?php

namespace Src\Company\Document\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequirementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        $id = $this->route('id');

        return [
            'title' => [
                'required',
                Rule::unique('project_requirements')->ignore($id),
                // 'unique:project_requirements',
            ],
            // 'document_file' => [
            //     'required',
            //     'file'
            // ],
            'project_id' => [
                'required',
                'exists:projects,id'
            ]
        ];
    }
}
