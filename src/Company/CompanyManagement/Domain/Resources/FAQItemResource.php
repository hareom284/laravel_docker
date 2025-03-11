<?php

namespace Src\Company\CompanyManagement\Domain\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class FAQItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        // Convert the string to a Carbon instance
        $carbonDate = Carbon::parse($this->created_at);

        // Format the date as you desire
        $formattedDate = $carbonDate->format('Y-m-d');

        return [
            'id' => $this->id,
            'question' => $this->question,
            'answer' => $this->answer,
            'status' => $this->status,
            'customer' => $this->customer ? $this->customer->first_name.' '. $this->customer->last_name : '',
            'project' => $this->project ? $this->project->property->street_name.' '. $this->project->property->unit_num : '',
            'created_at' => $formattedDate,
        ];
    }
}
