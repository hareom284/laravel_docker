<?php

namespace Src\Company\System\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyMobileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $logoUrl = $this->logo ? asset('storage/logo/' . $this->logo) : null;

        $stampUrl = $this->company_stamp ? asset('storage/stamp/' . $this->company_stamp) : null;

        return [
            'id' => $this->id,
            'name' => $this->name ?? '-',
            'tel' => $this->tel ?? '-',
            'fax' => $this->fax ?? '-',
            'email' => $this->email ?? '-',
            'main_office' => $this->main_office ?? '-',
            'design_branch_studio' => $this->design_branch_studio ?? '-',
            'hdb_license_no' => $this->hdb_license_no ?? '-',
            'reg_no' => $this->reg_no ?? '-',
            'gst_reg_no' => $this->gst_reg_no ?? '-',
            'gst' => $this->gst ?? '-',
            'logo' => $logoUrl,
            'company_stamp' => $stampUrl,
            'docu_prefix' => $this->docu_prefix ?? '',
            'invoice_no_start' => $this->invoice_no_start ?? '',
            'fy_start' => $this->fy_start ?? '',
            'fy_end' => $this->fy_end ?? '',
            'is_default' => $this->is_default ? true : false,
            'quotation_no' => $this->quotation_no,
            'customer_invoice_running_number_values' => $this->customer_invoice_running_number_values,
            'enable_customer_running_number_by_month' => $this->enable_customer_running_number_by_month ? true : false
        ];
    }
}
