<?php

namespace Src\Company\CompanyManagement\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFAQItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'question' => ['required','string'],
            'answer' => ['max:255']
        ];
    }
}