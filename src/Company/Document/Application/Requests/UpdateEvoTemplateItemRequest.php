<?php

namespace Src\Company\Document\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEvoTemplateItemRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            
            'description' => [
                'required'
            ],
            'unit_rate_with_gst' => [
                'nullable'
            ],
            'unit_rate_without_gst' => [
                'nullable'
            ]
        ];
    }
}
