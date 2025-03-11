<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderWithFilterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $salePerson = $this->staff->first_name . ' ' .  $this->staff->last_name;

        $project = $this->project->properties->block_num .' '. $this->project->properties->street_name . " #" . $this->project->properties->unit_num.' '.$this->project->properties->postal_code;

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

        $signature = null;
        if ($this->sales_rep_signature && Storage::disk('public')->exists('po/' . $this->sales_rep_signature)) {
            $signature = base64_encode(Storage::disk('public')->get('po/' . $this->sales_rep_signature));
        }
        
        $managerSignature = null;
        if ($this->manager_signature && Storage::disk('public')->exists('po/' . $this->manager_signature)) {
            $managerSignature = base64_encode(Storage::disk('public')->get('po/' . $this->manager_signature));
        }

        return [
            'id' => $this->id,
            'date' => $this->date,
            'time' => $this->time,
            'pages' => $this->pages,
            'purchase_order_number' => $this->purchase_order_number,
            'remark' => $this->remark,
            'delivery_date' => $this->delivery_date,
            'delivery_time_of_the_day' => $this->delivery_time_of_the_day,
            'sales_rep_signature' => $signature,
            'manager_signature' => $managerSignature,
            'status' => $status,
            'vendor_remark' => $this->vendor_remark,
            'invoice_no' => $this->invoice_no,
            'payment_amt' => $this->payment_amt,
            'discount_percentage' => $this->discount_percentage,
            'discount_amt' => $this->discount_amt,
            'credit_amt' => $this->credit_amt,
            'vendor_name' => $this->vendor->vendor_name,
            'created_date' => $this->created_at->format('Y-m-d'),
            'company_name' => $this->project->company->name,
            'sale_person' => $salePerson,
            'role' => $this->staff->roles->pluck('name')->toArray(),
            'customer_name' => $this->project->customer->first_name.' '. $this->project->customer->last_name,
            'customer_email' => $this->project->customer->email,
            'customer_phone' => $this->project->customer->prefix.' '.$this->project->customer->contact_no,
            'project' => $project,
            'invoice_no' => $this->project->invoice_no,
        ];
    }
}
