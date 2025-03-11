<?php

namespace Src\Company\System\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Company\CustomerManagement\Domain\Resources\GroupCustomerResources;

class AssignSalepersonsResources extends JsonResource
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
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'name_prefix' => $this->user->name_prefix,
            'customers' => GroupCustomerResources::collection($this->customers)

        ];
    }
}
