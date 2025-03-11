<?php

namespace Src\Company\Project\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierCostingRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'invoice_no' => ['required'],
            'payment_amt' => ['required'],
            'discount_percentage' => ['required'],
            'discount_amt' => ['required'],
            'credit_amt' => ['nullable'],
            'document_file' => [
                'nullable',
                'file',
                'mimes:jpg,png,pdf',
                'max:10240',
            ],
            'project_id' =>['required'],
            'vendor_id' =>['required'],
        ];
    }

    public function messages()
    {
        return [
            'document_file.max' => 'File must not be greater than 10 MB.',
        ];
    }
}