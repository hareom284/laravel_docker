<?php

namespace Src\Company\Project\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierDebitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {  
        if($this->is_gst_inclusive == 1){
            $totalAmount = $this->total_amount;
            $gstAmount = $this->gst_amount;
            $amount = $this->amount;
        }else{
            $totalAmount = $this->total_amount + $this->gst_amount;
            $gstAmount = "0.00";
            $amount = $this->amount;
        }

        $project = $this->saleReport->project->property->block_num .' '.$this->saleReport->project->property->street_name.' #'.$this->saleReport->project->property->unit_num .' '.$this->saleReport->project->property->postal_code;

        $documentUrl =  $this->pdf_path ? asset('storage/supplier_debits/' . $this->pdf_path) : "-";

        $invoiceDate = date('d-m-Y', strtotime($this->invoice_date));

        return [
            'id' => $this->id,
            'project' => $project,
            'invoice_no' => $this->invoice_no,
            'project_invoice' => $this->saleReport->project->invoice_no,
            'sale_report_id' => $this->sale_report_id,
            'description' => $this->description,
            'invoice_date' => $invoiceDate,
            'total_amount' => $totalAmount,
            'total_amount_display' => number_format($totalAmount, 2, '.', ','),
            'document_name' => $this->pdf_path ?? "-",
            'document_url' => $documentUrl,
            'document_file_status' => $this->pdf_path ? true : false,
            'amount' => $amount,
            'amount_display' => number_format($amount, 2, '.', ','),
            'is_gst_inclusive' => $this->is_gst_inclusive,
            'gst_amount' => $gstAmount,
            'gst_amount_display' => number_format($gstAmount, 2, '.', ','),
            'vendor_id' => $this->vendor_id,
            'company_name' => $this->vendor->vendor_name,
            'company_contact_phone' => $this->vendor->contact_person_number,
            'company_fax' => $this->vendor->fax_number,
        ];
    }
}
