<?php

namespace Src\Company\Document\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentStandardRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required'],
            'header_text' => ['required'],
            'footer_text' => ['required'],
            'company_id' => ['required','exists:companies,id']
        ];
    }
}
