<?php

namespace Src\Company\Project\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierDebitRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'invoice_no' => ['required'],
            'description' => ['required'],
            'is_gst_inclusive' => ['required'],
            'invoice_date' => ['required'],
            'amount' => ['required'],
            'gst_amount' => ['required'],
            'total_amount' =>['required'],
            'vendor_id' =>['required'],
            'sale_report_id' => ['required'],
        ];
    }
}