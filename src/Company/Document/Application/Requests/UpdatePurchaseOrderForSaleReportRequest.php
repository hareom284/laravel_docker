<?php

namespace Src\Company\Document\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseOrderForSaleReportRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'invoice_no' => [
                'required',
            ],
            'payment_amt' => [
                'required',
            ],
            'discount_percent' => [
                'required',
            ],
            'discount_amt' => [
                'required',
            ],
            'credit_amt' => [
                'required',
            ],
            'document_file' => [
                'required',
                'file'
            ],
            'vendor_remark' => [
                'nullable'
            ]
        ];
    }
}
