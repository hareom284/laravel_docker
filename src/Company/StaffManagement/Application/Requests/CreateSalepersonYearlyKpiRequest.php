<?php

namespace Src\Company\StaffManagement\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

use function PHPSTORM_META\map;

class CreateSalepersonYearlyKpiRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'year' => [
                'required',
                'string'
            ],
            'management_target' => [
                'required',
                'integer'
            ],
        ];
    }
}
