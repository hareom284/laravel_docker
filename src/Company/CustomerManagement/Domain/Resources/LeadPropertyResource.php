<?php

namespace Src\Company\CustomerManagement\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeadPropertyResource extends JsonResource
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
            'isDisabled' => $this->isDisabled,
            'street_name' => $this->street_name ? $this->street_name : '',
            'block_num' => $this->block_num ? $this->block_num : '',
            'unit_num' => $this->unit_num ? $this->unit_num : '',
            'postal_code' => $this->postal_code ? $this->postal_code : '',
            'property_id' => $this->pivot->property_id,
            'type_id' => $this->type_id,
            'type' => $this->propertyType->type,
        ];
    }
}
