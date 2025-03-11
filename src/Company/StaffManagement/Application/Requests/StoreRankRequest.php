<?php

namespace Src\Company\StaffManagement\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRankRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'rank_name' => [
                'required',
            ],
            'tier' => [
                'required',
                'unique:ranks',
            ],
            'commission_percent' => [
                'required'
            ]
        ];
    }
}
