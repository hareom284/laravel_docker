<?php

namespace Src\Company\Project\Domain\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierCostingPaymentDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        if($this->payment_type === 1){
            $paymentType = "Deposit Payment";
        }elseif ($this->payment_type === 2) {
            $paymentType = "1st Payment";
        }elseif ($this->payment_type === 3) {
            $paymentType = "2nd Payment";
        }elseif ($this->payment_type === 4) {
            $paymentType = "3rd Payment";
        }elseif ($this->payment_type === 5) {
            $paymentType = "Final Payment";
        }

        if($this->payment_method == 1){
            $paymentMethod = "Bank Transfer";
        } else if ($this->payment_method == 2){
            $paymentMethod = "CASH";
        } else if ($this->payment_method == 3){
            $paymentMethod = "PAYNOW";
        } else if ($this->payment_method == 4){
            $paymentMethod = "CHEQUE";
        } else if ($this->payment_method == 5){
            $paymentMethod = "NET";
        }


        $manager = $this->manager ? $this->manager->first_name.' '.$this->manager->last_name : "-";

        $managerPhone = $this->manager ? $this->manager->contact_no : "-";

        $managerSignature = $this->manager_signature ? base64_encode(file_get_contents(storage_path('app/public/supplier_costing/' . $this->manager_signature))) : null;

        if(count($this->supplierCostings) > 0){
            $supplierCostings = SupplierCostingResource::collection($this->supplierCostings);
        }else{
            $supplierCostings = SupplierCostingResource::collection($this->oldSupplierCostings);
        }
        
        return [
            'id' => $this->id,
            'payment_date' => Carbon::parse($this->payment_date)->format('Y-m-d'),
            'bank_trans_id' => $this->bank_transaction_id,
            'payment_type' => $paymentType,
            'amount' => $this->amount,
            'payment_method' => $paymentMethod,
            'status' => $this->status,
            'payment_made_by' => $this->accountant->first_name.' '.$this->accountant->last_name,
            'manager' => $manager,
            'manager_phone' => $managerPhone,
            'manager_sign' => $managerSignature,
            'invoices' => $supplierCostings,
            'remarks' => $this->remark,          
        ];
    }
}
