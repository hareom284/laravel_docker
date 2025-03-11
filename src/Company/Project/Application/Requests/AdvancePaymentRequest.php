<?php

namespace Src\Company\Project\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdvancePaymentRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => ['required'],
            'amount' => ['required'],
            'payment_date' => ['required'],
            'remark' => ['nullable'],
            'status' => ['nullable'],
            'user_id' => ['required'],
            'sale_report_id' => ['required'],
        ];
    }
}