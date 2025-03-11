<?php

namespace Src\Company\CustomerManagement\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeadCheckListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
           'check_list_template_item_id' => $this->id,
           'check_list_template_item_name' => $this->checklist_item_name,
           'status' => $this->pivot->status ? true : false,
           'date_completed' => $this->pivot->date_completed
        ];
    }
}
