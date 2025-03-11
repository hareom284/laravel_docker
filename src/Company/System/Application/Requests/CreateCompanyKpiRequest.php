<?php

namespace Src\Company\System\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

use function PHPSTORM_META\map;

class CreateCompanyKpiRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'period' => [
                'required',
                'string'
            ],
            'target' => [
                'required',
                'string'
            ],
            'company_id' => [
                'required',
                'integer'
            ],
        ];
    }
}
