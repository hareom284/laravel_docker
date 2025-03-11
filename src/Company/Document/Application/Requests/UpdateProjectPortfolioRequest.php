<?php

namespace Src\Company\Document\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectPortfolioRequest  extends FormRequest
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
            ],
            'title' => [
                'required',
            ],
            'description' => [
                'required'
            ],
            'document_file' => [
                'file',
                'mimes:jpg,png',
                'max:10240', // 10 MB limit (10240 KB)
            ],
        ];
    }

    public function messages()
    {
        return [
            'document_file.max' => 'File must not be greater than 10 MB.',
        ];
    }
}
