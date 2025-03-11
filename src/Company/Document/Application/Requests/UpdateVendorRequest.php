<?php

namespace Src\Company\Document\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'vendor_name' => [
                'required',
                'string',
            ],
            'contact_person' => [
                'nullable',
                'string'
            ],
            'contact_person_number' => [
                'nullable',
                'integer'
            ],
            'fax_number' => [
                'nullable',
                'integer'
            ],
            'rebate' => [
                'nullable',
            ],
            'fax_number' => [
                'nullable',
                'integer'
            ],
        ];
    }
}
