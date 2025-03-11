<?php

namespace Src\Company\Document\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendEmailCopyRequest  extends FormRequest
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
            'attachment' => [
                'required',
                'file'
            ],
            'email' => [
                'required',
                'email'
            ]
        ];
    }
}
