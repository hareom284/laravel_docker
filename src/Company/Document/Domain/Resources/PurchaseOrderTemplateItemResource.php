<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderTemplateItemResource extends JsonResource
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
            'vendor_category_id' => $this->vendorCategory->id,
            'vendor_category' => $this->vendorCategory->type,
            'company_id' => $this->company_id,
            'description' => $this->description,
            'quantity' => $this->quantity ?? "-",
            'size' => $this->size ?? "-",
            'code' => $this->code ?? "-",
        ];
    }
}
