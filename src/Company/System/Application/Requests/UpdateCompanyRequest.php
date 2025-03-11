<?php

namespace Src\Company\System\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'required'
            ],
            'tel' => [
                'required',
                'integer',
                'digits:8'
            ],
            'fax' => [
                'required',
                'integer',
                'digits:8'
            ],
            'email' => [
                'required',
                'email'
            ],
            'main_office' => [
                'required'
            ],
            'design_branch_studio' => [
                'nullable'
            ],
            'hdb_license_no' => [
                'nullable'
            ],
            'reg_no' => [
                'required'
            ],
            'gst_reg_no' => [
                'nullable'
            ],
            'gst' => [
                'required'
            ],
            'docu_prefix' => [
                'required'
            ],
            'invoice_no_start' => [
                'required'
            ],
            'fy_start' => [
                'required'
            ],
            'fy_end' => [
                'required'
            ],
            'quotation_no' => [
                // 'required'
            ],
        ];
    }
}
