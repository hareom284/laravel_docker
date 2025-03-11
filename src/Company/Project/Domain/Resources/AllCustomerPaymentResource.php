<?php

namespace Src\Company\Project\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AllCustomerPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $project = $this->saleReport->project->property->block_num .' '.$this->saleReport->project->property->street_name.' #'.$this->saleReport->project->property->unit_num.' '.$this->saleReport->project->property->postal_code;

        $customerName = $this->saleReport->project->customer->first_name . ' ' . $this->saleReport->project->customer->last_name;

        if(is_null($this->quick_book_invoice_id)){
            $unPaidPdfFilePath = $this->unpaid_invoice_file_path ? asset('storage/'.$this->unpaid_invoice_file_path) : "-";
            $unPaidPdfName = $this->unpaid_invoice_file_path ? "UnPaid - Invoice " . $this->paymentType->name : "-";

            $paidPdfFilePath = $this->paid_invoice_file_path ? asset('storage/'.$this->paid_invoice_file_path) : "-";
            $paidPdfName = $this->paid_invoice_file_path ? "Paid - Invoice " . $this->paymentType->name  : "-";
        }else{
            $unPaidPdfFilePath = $this->unpaid_invoice_file_path ? asset('storage/QBO/'.$this->unpaid_invoice_file_path) : "-";
            $unPaidPdfName = $this->unpaid_invoice_file_path ? "UnPaid - ".$this->unpaid_invoice_file_path : "-";

            $paidPdfFilePath = $this->paid_invoice_file_path ? asset('storage/QBO/'.$this->paid_invoice_file_path) : "-";
            $paidPdfName = $this->paid_invoice_file_path ? "Paid - ". $this->paid_invoice_file_path : "-";
        }

        return [
            'id' => $this->id,
            'project' => $project,
            'customer_name' => $customerName,
            'amount' => $this->amount,
            'description' => $this->description,
            'remark' => $this->remark,
            'status' => $this->status === 1 ? true : false ,
            'invoice_no' => $this->invoice_no ? $this->invoice_no : " - ",
            'type' => $this->paymentType->name,
            'unpaid_pdf' => $unPaidPdfFilePath,
            'unpaid_pdf_name' => $unPaidPdfName,
            'paid_pdf' => $paidPdfFilePath,
            'paid_pdf_name' => $paidPdfName,
            'payment_type' => $this->payment_type,
            'sale_report_id' => $this->sale_report_id
        ];
    }
}
