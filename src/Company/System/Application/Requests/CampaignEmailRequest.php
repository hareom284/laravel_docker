<?php

namespace Src\Company\System\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CampaignEmailRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'customer_data' => [
                'required'
            ],
            'email_content' => [
                'required'
            ],
            'campaign_title' => [
                'required'
            ]
        ];
    }
}
