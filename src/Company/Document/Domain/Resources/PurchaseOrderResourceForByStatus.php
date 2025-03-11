<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResourceForByStatus extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // project is the same with the full address of the project property
        $project = $this->project->property->block_num .' '.$this->project->property->street_name.' #'.$this->project->property->unit_num.' '.$this->project->property->postal_code;

        return [
            'id' => $this->id,
            'invoice_no' => $this->vendor_invoice ? $this->vendor_invoice->invoice_no : "-",
            'payment_amt' => $this->vendor_invoice ? $this->vendor_invoice->payment_amt : "-",
            'status' => $this->status,
            'created_date' => $this->created_at,
            'project' => $project,
            'manager_signature' => $this->manager_signature,
            'customer_name' => $this->project->customer->first_name.' '. $this->project->customer->last_name,
            'customer_email' => $this->project->customer->email,
            'sale_name' => $this->staff->first_name.' '.$this->staff->last_name,
            'role' => $this->staff->roles->pluck('name')->toArray(),
            // 'role' => $this->staff->roles
            // 'project' => $this->project ? $this->project : null,
            // 'staff' => $this->staff ? $this->staff : null
        ];
    }
}
