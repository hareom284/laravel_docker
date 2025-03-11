<?php

namespace Src\Company\Project\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierCostingRequest  extends FormRequest
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
            'payment_amt' => ['required'],
            'discount_percentage' => ['required'],
            'discount_amt' => ['required'],
            'credit_amt' => ['nullable'],
            'amount_paid' => ['nullable'],
            'to_pay' => ['nullable'],
            'document_file' => [
                'nullable',
                'file',
                'mimes:jpg,png,pdf',
                'max:10240',
            ],
            'project_id' =>['nullable'],
            'vendor_id' =>['required'],
            'vendor_invoice_expense_type_id' => ['nullable'],
            'quick_book_expense_id' => ['nullable'],
        ];
    }

    public function messages()
    {
        return [
            'document_file.max' => 'File must not be greater than 10 MB.',
        ];
    }
}