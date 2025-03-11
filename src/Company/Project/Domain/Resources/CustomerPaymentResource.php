<?php

namespace Src\Company\Project\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
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

        $refundDate = is_null($this->refund_date) ? null : date('d-m-Y', strtotime($this->refund_date));
        $refudPdfFilePath = !is_null($this->credit_note_file_path) ? asset('storage/QBO/'.$this->credit_note_file_path) : "-";
        $refundPdfName = !is_null($this->credit_note_file_path) ? "Refund - ".$this->credit_note_file_path : "-";

        return [
            'id' => $this->id,
            'sale_report_id' => $this->sale_report_id,
            'amount' => $this->amount,
            'invoice_no' => $this->invoice_no,
            'index' => $this->index,
            'description' => $this->description,
            'remark' => $this->remark,
            'unpaid_pdf' => $unPaidPdfFilePath,
            'unpaid_pdf_name' => $unPaidPdfName,
            'paid_pdf' => $paidPdfFilePath,
            'paid_pdf_name' => $paidPdfName,
            'status' => $this->status === 1 ? true : false,
            'type' => $this->paymentType->name,
            'is_refund' => $this->is_refunded === 1 ? true : false,
            'refund_amount' => $this->refund_amount,
            'refund_date' => $refundDate,
            'refund_remak' => $this->refund_reason,
            'payment_type' => $this->payment_type,
            'refund_pdf' => $refudPdfFilePath,
            'refund_pdf_name' => $refundPdfName,
        ];
    }
}
