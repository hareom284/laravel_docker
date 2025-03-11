<?php

namespace Src\Company\Document\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDesignWorkRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $project_id = request('project_id');
        
        return [
            'name' => [
                'required',
                Rule::unique('design_works')->where(function ($query) use ($project_id) {
                    return $query->where('project_id', $project_id);
                }),
                // 'unique:design_works'
            ],
            'document_file' => [
                'required',
                'file'
            ],
            'scale' => [
                'nullable',
                'string'
            ],
            'project_id' => [
                'required',
                'exists:projects,id'
            ]
            // 'document_date' => [
            //     'nullable',
            //     'date'
            // ],
            // 'request_status' => [
            //     'nullable',
            //     'integer'
            // ],
            // 'signed_date' => [
            //     'nullable',
            //     'date'
            // ],
        ];
    }
}
