<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderMobileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $status = '';

        switch ($this->status) {
            case 1:
                $status = 'NEW';
                break;
            case 2:
                $status = 'PENDING APPROVAL';
                break;
            case 3:
                $status = 'APPROVED';
                break;
            default:
                $status = 'NEW';
                break;
        }

        return [
            'id' => $this->id,
            'title' => "Purchase Order - " . $this->purchase_order_number,
            'status' => $status,
            'item_count' => count($this->poItems),
            'created_date' => $this->created_at
        ];
    }
}
