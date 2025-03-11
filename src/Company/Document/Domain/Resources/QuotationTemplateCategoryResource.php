<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationTemplateCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'salesperson' => $this->salesperson,
            'salesperson_id' => $this->salesperson_id,
            'has_template' => true,
            'quotation_template_count' => count($this->quotationTemplates)
        ];

        return $data;
    }
}
