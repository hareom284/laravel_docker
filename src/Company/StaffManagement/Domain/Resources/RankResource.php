<?php

namespace Src\Company\StaffManagement\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RankResource extends JsonResource
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
            'rank_name' => $this->rank_name,
            'tier' => $this->tier,
            'commission_percent' => $this->commission_percent,
            'or_percent' => $this->or_percent
        ];
    }
}
