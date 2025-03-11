<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'vendor_name' => $this->vendor_name,
            'postal_code' => $this->postal_code,
            'block_num' => $this->block_num,
            'street_name' => $this->street_name,
            'unit_num' => $this->unit_num,
            'contact_person_number' => $this->contact_person_number,
            'fax_number' => $this->fax_number,
            'contact_person_last_name' => $this->contact_person_last_name,
            'prefix' => $this->prefix,
            'name_prefix' => $this->name_prefix,
            'contact_person_name' => str_replace('-', ' ', $this->contact_person),
            'contact_person' => $this->contact_person,
            'vendor_category_id' => $this->vendor_category_id,
            'vendor_category' => $this->vendorCategory ? $this->vendorCategory->type : null,
            'rebate' => $this->rebate,
            'email' => $this->email,
            'user_id' => $this->user_id,
        ];
    }
}
