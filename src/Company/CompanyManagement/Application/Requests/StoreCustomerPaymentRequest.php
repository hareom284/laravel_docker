<?php

namespace Src\Company\CompanyManagemen\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerPaymentRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'invoice_no' => ['required'],
            'invoice_no' => ['required'],
            'invoice_no' => ['required'],
        ];
    }
}