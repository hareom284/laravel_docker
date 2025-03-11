<?php

namespace Src\Company\UserManagement\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SurveyAnswerRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'property_type' => 'required|string',
            'kitchen_work' => 'required|string',
            'preferred_style' => 'required|string',
            'floor_plan' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
