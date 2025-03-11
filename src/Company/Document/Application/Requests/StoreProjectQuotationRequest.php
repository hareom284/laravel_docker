<?php

namespace Src\Company\Document\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectQuotationRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'project_id' => ['required'],
            'total_amount' => ['required'],
            // 'salesperson_signature' => ['required'],
            // 'customer_signature' => ['required']
        ];
    }
}
