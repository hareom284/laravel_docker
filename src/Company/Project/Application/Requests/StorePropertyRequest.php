<?php

namespace Src\Company\Project\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => ['required'],
            'street_name' => ['required'],
            'block_num' => ['required'],
            'unit_num' => ['required'],
            'postal_code' => ['required']
        ];
    }
}