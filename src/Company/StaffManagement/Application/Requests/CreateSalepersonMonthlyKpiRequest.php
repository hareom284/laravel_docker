<?php

namespace Src\Company\StaffManagement\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

use function PHPSTORM_META\map;

class CreateSalepersonMonthlyKpiRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'saleperson_id' => [
                'required'
            ],
            'year' => [
                'required'
            ],
            'month' => [
                'required'
            ],
            'target' => [
                'required'
            ],
        ];
    }
}
