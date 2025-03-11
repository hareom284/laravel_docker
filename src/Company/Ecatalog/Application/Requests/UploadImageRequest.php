<?php

namespace Src\Company\Ecatalog\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'images.*' => 'required|image|mimes:jpeg,bmp,png',

        ];

        return $rules;

    }

    public function messages()
    {
        return [
            'images.*' => "Images must be png, jpeg or bmp",
        ];
    }

}
