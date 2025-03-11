<?php

namespace Src\Company\Project\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefundCustomerPaymentRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'amount' => ['required'],
            'remark' => ['required'],
            'refund_date' => ['required'],
        ];
    }
}