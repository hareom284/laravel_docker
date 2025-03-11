<?php

namespace Src\Company\Document\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobSheetUploadRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'document_file' => ['required', 'array'],
            'document_file.*' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png']
        ];
    }
}