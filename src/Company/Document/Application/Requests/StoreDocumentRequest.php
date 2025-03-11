<?php

namespace Src\Company\Document\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest  extends FormRequest
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
                // 'unique:documents',
            ],
            'file_type' => [
                'nullable',
                'min:3',
            ],
            'attachment_file' => [
                'required',
                'file',
                'mimes:jpg,png,pdf',
                'max:10240',
            ],
            'allow_customer_view' => [
                'required'
            ],
            'folder_id' => [
                'nullable',
            ],
            'project_id' => [
                'required',
                'exists:projects,id'
            ]
        ];
    }

    public function messages()
    {
        return [
            'document_file.max' => 'File must not be greater than 10 MB.',
        ];
    }
}
