<?php

namespace Src\Company\CompanyManagement\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncDataRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'syncEntity' => ['required'],
            'syncCompany' => ['required'],
        ];
    }
}