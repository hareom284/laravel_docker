<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderByProjectResource extends JsonResource
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
            'vendor_name' => $this->vendor->vendor_name,
            'purchase_order_number' => $this->purchase_order_number,
            'status' => $status,
            'invoice_no' => $this->invoice_no ?? "",
            'payment_amt' => $this->payment_amt ?? "",
            'discount_percentage' => $this->discount_percentage ?? "",
            'discount_amt' => $this->discount_amt ?? "",
            'credit_amt' => $this->credit_amt ?? "",
            'document_file' => $this->document_file ?? "",
        ];
    }
}
