<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AreaOfWorkResource extends JsonResource
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
            'section_id' => $this->section_id,
            'index' => $this->index,
            'name' => $this->name,
            'items' => QuotationTemplateItemsResource::collection($this->items)
        ];
    }
}
