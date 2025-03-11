<?php

namespace Src\Company\Project\Domain\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;

class ProjectForAccoutantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request)
    {
        $saleperson = [];

        foreach ($this->salespersons as $value) {
            $arr = new stdClass();
            $arr->name = $value->first_name . " " . $value->last_name;
            $arr->email = $value->email;
            array_push($saleperson, $arr);
        }

        $property = $this->properties;
        $created_at = $this->created_at ? Carbon::parse($this->created_at)->format('d/m/Y') : null;
        return [
            'id' => $this->id,
            'name' => $property->block_num.' '.$property->street_name.' #'.$property->unit_num.' Singapore '.$property->postal_code,
            'invoice_no' => $this->invoice_no,
            'status' => $this->project_status,
            'customer_name' => $this->customers->first_name . " " . $this->customers->last_name,
            'customer_email' => $this->customers->email,
            'salepersons' => $saleperson,
            'total_amount' => $this->saleReport ? $this->saleReport->total_sales : 0,
            'paid_amount' => $this->saleReport ? $this->saleReport->paid : 0,
            'remaining_amount' => $this->saleReport ? $this->saleReport->remaining : 0,
            'freezed' => $this->freezed,
            'request_note' => $this->request_note,
            'payment_status' => $this->payment_status,
            'sale_report' => $this->saleReport,
            'created_at' => $created_at
        ];
    }
}
