<?php

namespace Src\Company\Project\Domain\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierCostingPaymentListResource extends JsonResource
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

        if($this->payment_method === 1){
            $paymentMethod = "Bank Transfer";
        }elseif ($this->payment_method === 2) {
            $paymentMethod = "CASH";
        }elseif ($this->payment_method === 3) {
            $paymentMethod = "PAYNOW";
        }elseif ($this->payment_method === 4) {
            $paymentMethod = "CHEQUE";
        }elseif ($this->payment_method === 5) {
            $paymentMethod = "NETS";
        }


        return [
            'id' => $this->id,
            'bank_transaction_id' => $this->bank_transaction_id,
            'payment_date' => Carbon::parse($this->payment_date)->format('Y-m-d'),
            'payment_type' => $paymentType,
            'payment_method' => $paymentMethod,
            'amount' => $this->amount,
            'status' => $this->status,
            'count_invoice' => count($this->supplierCostings)
        ];
    }
}
