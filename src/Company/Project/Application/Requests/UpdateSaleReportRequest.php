<?php

namespace Src\Company\Project\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSaleReportRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'total_cost' => ['required'],
            'total_sales' => ['required'],
            'comm_issued' => ['required'],
            'or_issued' => ['required'],
            'special_discount' => ['nullable'],
            'gst' => ['required'],
            'rebate' => ['nullable'],
            'net_profit_and_loss' => ['required'],
            'carpentry_job_amount' => ['nullable'],
            'carpentry_cost' => ['nullable'],
            'carpentry_comm' => ['nullable'],
            'carpentry_special_discount' => ['nullable'],
            'net_profit' => ['required']
        ];
    }
}