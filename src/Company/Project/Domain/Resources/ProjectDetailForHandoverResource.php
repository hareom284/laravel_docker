<?php

namespace Src\Company\Project\Domain\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use stdClass;

class ProjectDetailForHandoverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $companies = new stdClass();
        $companies->id = $this->company->id;
        $companies->name = $this->company->name;
        $company_base64Image = '';
        if($this->company->logo)
        {
            $customer_file_path = 'logo/' . $this->company->logo;

            $company_image = Storage::disk('public')->get($customer_file_path);

            $company_base64Image = base64_encode($company_image);
        }
        $companies->company_logo = $company_base64Image;

        $datetime = $this->created_at;
        $formattedDate = Carbon::parse($datetime)->format('d/m/Y');

        return [
            'id' => $this->id,
            // 'ref_no' => $this->ref_no,
            'agreement_no' => $this->agreement_no,
            'date' => $this->completed_date ? $this->completed_date : $formattedDate,
            'reg_no' => $this->company->reg_no,
            'gst_reg_no' => $this->company->gst_reg_no,
            'hdb_license_no' => $this->company->hdb_license_no,
            'agreement_amount' => $this->renovation_documents,
            'progressive_payments' => $this->saleReport->customer_payments,
            'customer' => $this->customer->name_prefix.' '.$this->customer->first_name.' '.$this->customer->last_name,
            'address' => $this->property->block_num.' '.$this->property->street_name.' '.$this->property->unit_num,
            'spore' => $this->property->postal_code,
            'customer_id' => $this->customer_id,
            'customer_mobile' => $this->customer->contact_no,
            'paid' => $this->saleReport->paid,
            'remaining' => $this->saleReport->remaining,
            'nric' => $this->customer->customers->nric,
            'company' => $companies,
            'full_address' => $this->property->block_num.' '.$this->property->street_name.' '.$this->property->unit_num.' '.$this->property->postal_code,
        ];
    }
}
