<?php

namespace Src\Company\Document\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRenovationDocumentSignRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'customer_signature' => ['required']
        ];
    }
}