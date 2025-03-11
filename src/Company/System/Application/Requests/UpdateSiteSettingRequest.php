<?php

namespace Src\Company\System\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'site_name' => [
                'required'
            ],
            'ssl' => [
                'nullable'
            ],
            'timezone' => [
                'nullable'
            ],
            'locale' => [
                'nullable'
            ],
            'url' => [
                'required'
            ],
            'email' => [
                'required'
            ],
            'contact_number' => [
                'required',
                'integer',
                'digits:8'
            ],
            'website_logo' => [
                'nullable'
            ],
            'website_favicon' => [
                'nullable'
            ]
        ];
    }
}
