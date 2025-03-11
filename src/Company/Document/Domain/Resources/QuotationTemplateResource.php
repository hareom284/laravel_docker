<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class QuotationTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return [
        //     'id' => $this->id,
        //     'name' => $this->name,
        //     'index' => $this->index,
        //     'calculation_type' => $this->calculation_type,
        //     'vendors' => VendorResource::collection($this->vendors),
        //     'area_of_works' => AreaOfWorkResource::collection($this->areaOfWorks),
        // ];

        $vendors = $this->vendors->pluck('id')->toArray();
        $vendor_categories = DB::table('section_vendor')
        ->where('section_id', $this->id)
        ->distinct()
        ->pluck('vendor_category_id')
        ->toArray();
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'index' => $this->index,
            'calculation_type' => $this->calculation_type,
            'vendors' => $vendors,
            'vendor_categories' => $vendor_categories,
            'area_of_works' => AreaOfWorkResource::collection($this->areaOfWorks),
            'is_misc' => $this->is_misc ?? false,
            'description' => $this->description,
        ];
    }
}
