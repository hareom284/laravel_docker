<?php

namespace Src\Company\Project\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorInvoiceExpenseTypeResource extends JsonResource
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
            'name' => $this->name,
            'project_related' => $this->project_related == 1 ? true : false,
        ];
    }
}
