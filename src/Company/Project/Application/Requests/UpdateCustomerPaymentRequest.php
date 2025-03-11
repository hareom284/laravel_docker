<?php

namespace Src\Company\Project\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerPaymentRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'payment_type' => ['required'],
            'amount' => ['required'],
            'remark' => ['nullable'],
            'bank_info' => ['nullable'],
            'invoice_date' => ['nullable'],
        ];
    }
}