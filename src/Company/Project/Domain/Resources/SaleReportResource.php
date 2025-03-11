<?php

namespace Src\Company\Project\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request)
    {
            $projectProperty = $this?->project?->property;
            $projectAddress = '';
            if($projectProperty){
                $addressParts = [
                    $projectProperty->block_num,
                    $projectProperty->street_name,
                ];
                if (!empty($projectProperty->unit_num)) {
                    $addressParts[] = '#' . $projectProperty->unit_num;
                }
                $addressParts = array_filter($addressParts);
                if (!empty($projectProperty->postal_code)) {
                    $addressParts[] = $projectProperty->postal_code;
                }
                $projectAddress = implode(' ', $addressParts);
            }
        return [
            'id' => $this->id,
            'file_status' => $this->file_status,
            'project_id' => $this->project_id,
            'document_file' => $this->document_file,
            'project_address' => $projectAddress,
            'commissions' => $this->saleCommissions
        ];
    }
}
