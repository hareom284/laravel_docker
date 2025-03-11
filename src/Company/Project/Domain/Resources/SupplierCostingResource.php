<?php

namespace Src\Company\Project\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierCostingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $salePersons = [];

        if(is_null($this->project_id)){
            $project = "Non Related Project Expense";
            $projectInvoice = "-";
        }else{
            $project = $this->project->property->block_num .' '.$this->project->property->street_name.' #'.$this->project->property->unit_num.' '.$this->project->property->postal_code;
            $projectInvoice = $this->project->invoice_no;

            foreach($this->project->salespersons as $salePerson){
                $salePersons[] = $salePerson->first_name . ' ' . $salePerson->last_name;
            }
        }
        $documentUrl =  $this->document_file ? asset('storage/supplier_costings/' . $this->document_file) : "";


        $statusLists = [
            0 => "Verifying",
            1 => "Pending Approval",
            2 => "Approved",
            3 => "Paid",
        ];

        $statusString = isset($statusLists[$this->status]) ? $statusLists[$this->status] : "";

        $approved_by = $this->approvals()->pluck('approved_by');

        if(!is_null($this->vendor_payment_id) || count($this->payments) > 0){

            $statusString = $statusLists[3];
        }

        if(is_null($this->invoice_date)){
            $paymentDate = date('d-m-Y', strtotime($this->created_at));
        }else{
            $paymentDate = date('d-m-Y', strtotime($this->invoice_date));
        }

        if(($this->amount_paid + $this->discount_amt) == $this->payment_amt ){
            $paidStatus = true;
        }else{
            $paidStatus = false;
        }

        if (($this->amount_paid + $this->discount_amt) != $this->payment_amt && $this->status == 3) {
            $statusString = 'Partially Paid';
        }

        if(!is_null($this->vendor_payment_id)){
            $payment = $this->oldPayment;
        }elseif(count($this->payments) > 0){
            $payment = $this->payments;
        }else{
            $payment = null;
        }


        return [
            'id' => $this->id,
            'purchase_order_id' => $this->purchase_order_id,
            'purchase_order_no' => $this->purchaseOrder ? $this->purchaseOrder->purchase_order_number : "-",
            'project_id' => $this->project_id,
            'project_invoice' => $projectInvoice,
            'project' => $project,
            'vendor_id' => $this->vendor_id,
            'vendor' => $this->vendor,
            'company_name' => $this->vendor->vendor_name,
            'company_fax' => $this->vendor->fax_number,
            'company_contact_phone' => $this->vendor->contact_person_number,
            'invoice_no' => $this->invoice_no ?? "",
            'payment_amt' => $this->payment_amt,
            'amount_paid' => $this->amount_paid,
            'to_pay' => $this->to_pay,
            'paid_status' => $paidStatus,
            'gst_value' => $this->gst_value,
            'discount_percentage' => $this->discount_percentage ?? "",
            'discount_amt' => $this->discount_amt,
            'credit_amt' => $this->credit_amt,
            'document_name' => $this->document_file ?? "",
            'document_file' => $documentUrl,
            'payment_date' => $paymentDate,
            'payment' => $payment,
            'amended_amt' => $this->amended_amt ?? 0.00,
            'remark' => $this->remark ?? "",
            'status' => $this->status,
            'status_string' => $statusString,
            'document_file_status' => $this->document_file ? true : false,
            'expense_type_id' => is_null($this->vendor_invoice_expense_type_id) ? "-" : $this->vendor_invoice_expense_type_id,
            'expense_type' => is_null($this->vendor_invoice_expense_type_id) ? "-" : $this->expenseType->name,
            'quick_book_expense_id' => $this->quick_book_expense_id,
            'description' => $this->description,
            'approved_by' => $approved_by,
            'is_gst_inclusive' => $this->is_gst_inclusive == 1 ? true : false,
            'salepersos' => $salePersons
        ];
    }
}
