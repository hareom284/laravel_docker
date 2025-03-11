<?php

namespace Src\Company\Document\Domain\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;

class PendingRenoDocResource extends JsonResource
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

        foreach ($this->projects->salespersons as $value) {
            $arr = new stdClass();
            $arr->name = $value->first_name . " " . $value->last_name;
            $arr->email = $value->email;
            $arr->roles = $value->roles;
            array_push($saleperson, $arr);
        }

        $property = $this->projects->properties;
        $created_at = $this->created_at ? Carbon::parse($this->created_at)->format('d/m/Y') : null;
        return [
            'id' => $this->id,
            'name' => $property->block_num.' '.$property->street_name.' #'.$property->unit_num.' Singapore '.$property->postal_code,
            'status' => $this->status,
            'customer_name' => $this->projects->customers->first_name . " " . $this->projects->customers->last_name,
            'customer_email' => $this->projects->customers->email,
            'salepersons' => $saleperson,
            'total_amount' => $this->projects->saleReport ? $this->projects->saleReport->total_sales : 0,
            'paid_amount' => $this->projects->saleReport ? $this->projects->saleReport->paid : 0,
            'remaining_amount' => $this->projects->saleReport ? $this->projects->saleReport->remaining : 0,
            'created_at' => $created_at,
            'document_type' => $this->type,
            'sale_report' => $this->projects->saleReport,
            'project_id' => $this->projects->id,
            'reno_total_amount' => $this->total_amount,
            'version_number' => $this->version_number
        ];
    }
}
