<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RenovationSubItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //check if there has area of work name
        if (isset($this->renovation_area_of_work->name))
            $areaOfWorkName = $this->renovation_area_of_work->name;
        else
            $areaOfWorkName = $this->renovation_item_area_of_work_id ? $this->renovation_area_of_work->areaOfWork->name : '';

        return [
            'id' => $this->id,
            'name' => $this->name,
            'calculation_type' => $this->renovation_sections->sections->calculation_type,
            'quantity' => $this->quantity,
            'current_quantity' => isset($this->current_quantity) ? $this->current_quantity : null,
            'lengthmm' => $this->length,
            'breadthmm' => $this->breadth,
            'heightmm' => $this->height,
            'measurement' => $this->unit_of_measurement,
            'is_fixed_measurement' => $this->is_fixed_measurement,
            'price' => $this->price,
            'cost_price' => $this->cost_price,
            'profit_margin' => $this->profit_margin,
            'is_FOC' => $this->is_FOC,
            'section_id' => $this->renovation_item_section_id,
            'section_name' => $this->renovation_sections->sections->name,
            'area_of_work_id' => $this->renovation_item_area_of_work_id,
            'area_of_work_name' => $areaOfWorkName,
            'is_excluded' => $this->excluded,
            'items' => $this->items
        ];
    }
    
}
