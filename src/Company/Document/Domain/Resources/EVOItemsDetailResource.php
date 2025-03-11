<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EVOItemsDetailResource extends JsonResource
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
            'item_id' => $this->id,
            'item_name' => $this->item_description,
            'total_qty' => $this->quantity,
            'unit_rate' => $this->unit_rate,
            'total_amount' => $this->total,
            'rooms' => EVOItemRoomsDetailResource::collection($this->rooms)
        ];
    }
}
