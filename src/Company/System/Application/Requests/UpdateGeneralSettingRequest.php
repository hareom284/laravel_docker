<?php

namespace Src\Company\System\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeneralSettingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'settingsValues' => [
                'required'
            ]

        ];
    }
}
