<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EVOItemRoomsDetailResource extends JsonResource
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
            'room_id' => $this->pivot->room_id,
            'room_name' => $this->pivot->name ? $this->pivot->name : $this->room_name,
            'room_qty' => $this->pivot->quantity
        ];
    }
}
