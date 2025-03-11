<?php

namespace Src\Company\Project\Domain\Resources;

use Faker\Core\Number;
use Illuminate\Http\Resources\Json\JsonResource;

class AdvancePaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $paymentDate = date('d-m-Y', strtotime($this->payment_date));
        $projectAddress = $this->saleReport->project->property->block_num .' '.$this->saleReport->project->property->street_name.' #'.$this->saleReport->project->property->unit_num.' '.$this->saleReport->project->property->postal_code;

        return [
            'id' => $this->id,
            'project_address' => $projectAddress,
            'title' => $this->title,
            'amount_for_show' => number_format($this->amount, 2),
            'amount' => $this->amount,
            'payment_date' => $paymentDate,
            'remark' => $this->remark,
            'status' => $this->status,
            'status_string' => $this->status == 0 ? 'Did Not Re-Paid' : 'Re-Paid',
            'saleperson_id' => $this->user_id,
            'saleperson' => $this->salePerson->first_name . ' ' . $this->salePerson->last_name,
            'sale_report_id' => $this->sale_report_id,
        ];
    }
}
