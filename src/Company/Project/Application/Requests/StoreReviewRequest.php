<?php

namespace Src\Company\Project\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest  extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => [
                'required'
            ],
            'comments' => [
                'nullable'
            ],
            'stars' => [
                'required',
                'integer'
            ],
            'date' => [
                'required'
            ],
            'project_id' => [
                'required',
                'exists:projects,id'
            ],
            'salesperson_id' => [
                'required',
                'exists:staffs,id'
            ]
        ];
    }
}