<?php

namespace Src\Company\Project\Application\Requests;

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
            'payment_type' => ['required'],
            'invoice_no' => ['nullable'],
            'invoice_date' => ['nullable'],
            'description' => ['nullable'],
            'amount' => ['required'],
            'remark' => ['nullable'],
            'sale_report_id' => ['required']
            
        ];
    }
}