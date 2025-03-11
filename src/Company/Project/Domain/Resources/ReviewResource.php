<?php

namespace Src\Company\Project\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'id' => $this->id,
            'title' => $this->title,
            'comments' => $this->comments,
            'stars' => $this->stars,
            'date' => $this->date,
            'project' => $this->project->property->unit_num.' '.$this->project->property->street_name.' '.$this->project->property->block_num.' '.$this->project->property->postal_code,
            'saleperson_name' => $this->saleperson->first_name.' '.$this->saleperson->last_name,
            'customer_name' => $this->customer->first_name.' '.$this->customer->last_name
        ];
    }
}
