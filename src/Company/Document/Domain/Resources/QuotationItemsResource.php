<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationItemsResource extends JsonResource
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
            'description' => $this->description,
            'index' => $this->index,
            'section_id' => $this->sections->id,
            'section_name' => $this->sections->name,
            'is_misc' => $this->sections->is_misc,
            'calculation_type' => $this->sections->calculation_type,
            'section_index' => $this->sections->index,
            'area_of_work_id' => $this->areaOfWorks ? $this->areaOfWorks->id : null,
            'area_of_work_name' => $this->areaOfWorks ? $this->areaOfWorks->name : "",
            'area_of_work_index' => $this->areaOfWorks ? $this->areaOfWorks->index : null,
            'unit_of_measurement' => $this->unit_of_measurement,
            'is_fixed_measurement' => $this->is_fixed_measurement ?? 0,
            'quantity' => $this->quantity,
            'price_without_gst' => $this->price_without_gst,
            'price_with_gst' => $this->price_with_gst,
            'cost_price' => $this->cost_price,
            'profit_margin' => $this->profit_margin,
            'section_description' => $this->sections->description
        ];
    }
}
