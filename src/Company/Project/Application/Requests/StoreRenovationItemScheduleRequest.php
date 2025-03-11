<?php

namespace Src\Company\Project\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRenovationItemScheduleRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'project_id' => [
                'required',
                'exists:projects,id'
            ],
            'renovation_item_id' => [
                'required',
                'array'
            ],
            'start_date' => [
                'required'
            ],
            'end_date' => [
                'required'
            ],
            'show_in_timeline' => [
                'required'
            ]
        ];
    }
}