<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderWithItemResource extends JsonResource
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

        // $signature = $this->sales_rep_signature ? base64_encode(file_get_contents(storage_path('app/public/po/' . $this->sales_rep_signature))) : null;
        // $managerSignature = $this->manager_signature ? base64_encode(file_get_contents(storage_path('app/public/po/' . $this->manager_signature))) : null;

        $signature = null;
        if ($this->sales_rep_signature && Storage::disk('public')->exists('po/' . $this->sales_rep_signature)) {
            $signature = base64_encode(Storage::disk('public')->get('po/' . $this->sales_rep_signature));
        }
        
        $managerSignature = null;
        if ($this->manager_signature && Storage::disk('public')->exists('po/' . $this->manager_signature)) {
            $managerSignature = base64_encode(Storage::disk('public')->get('po/' . $this->manager_signature));
        }

        $address = $this->project->properties->block_num . ' ' . $this->project->properties->street_name . "#" . $this->project->properties->unit_num;

        $company = $this->project->company->name;

        $time = $this->time ? $this->time : " ";

        $pdf_file = "";

        if ($this->pdf_file) {
            $pdf_file =  asset('storage/pdfs/' . $this->pdf_file);
        }

        return [
            'id' => $this->id,
            'date' => $this->date,
            'time' => $time,
            'pages' => $this->pages ?? " ",
            'attn' => $this->attn ?? " ",
            'purchase_order_number' => $this->purchase_order_number,
            'remark' => $this->remark,
            'delivery_date' => $this->delivery_date,
            'delivery_time_of_the_day' => $this->delivery_time_of_the_day,
            'status' => $status,
            'created_date' => $this->created_at,
            'sales_rep_signature' => $signature,
            'vendor_id' => $this->vendor_id,
            'vendor_name' => $this->vendor->vendor_name,
            'vendor_person' => $this->vendor->contact_person,
            'vendor_fax' => $this->vendor->fax_number,
            'item_count' => count($this->poItems),
            'items' => $this->poItems,
            'project_id' => $this->project->id,
            'project_street' => $this->project->properties->street_name,
            'address' => $address,
            'company' => $company,
            'staff' => $this->staff->first_name,
            'staff_no' => $this->staff->contact_no,
            'manager_signature' => $managerSignature,
            'manager' => $this->manager->first_name ?? "-",
            'manager_no' => $this->manager->contact_no ?? "-",
            'pdf_file' => $pdf_file
        ];
    }
}
