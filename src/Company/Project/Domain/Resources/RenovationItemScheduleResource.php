<?php

namespace Src\Company\Project\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RenovationItemScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {   
        $itemName = $this->renovationItem->name;

        $areaOfWork = $this->renovationItem->renovation_area_of_work;
        $area = $areaOfWork ? $areaOfWork->areaOfWork->name : "";
        $areaId = $areaOfWork ? $areaOfWork->areaOfWork->id : "";
        
        return [
            'schedule_id' => $this->id,
            'previous_item_id' => $this->renovationItem->prev_item_id,
            'sectionId' => $this->renovationItem->renovation_sections->sections->id,
            'sectionName' => $this->renovationItem->renovation_sections->sections->name,
            'item_id' => $this->renovationItem->id,
            'item_name' => $itemName ,
            'areaId' => $areaId ,
            'areaName' => $area ,
            'item_type' => $this->renovationItem->renovation_documents->type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'isChecked' => $this->is_checked,
            'template_item_id' => $this->renovationItem->quotation_template_item_id,
            'isCancel' => $this->isCancel,
            'parent_id' => $this->renovationItem->parent_id,
            'is_include' => $this->renovationItem->is_excluded === 1 ? false : true,
            'document_id' => $this->renovationItem->renovation_document_id,
        ];
    }
}
