<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DesignWorkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $documentUrl = $this->document_file ? asset('storage/design_work_file/' . $this->document_file) : null;

        $extension = strtolower(pathinfo($documentUrl, PATHINFO_EXTENSION));

        if($extension == 'svg' || $extension == 'jpg' || $extension == 'png' || $extension == 'jpeg' || $extension == 'JPEG'){
            
            $icon = config('document-icons.image');

        } else if($extension == 'docx'){
            
            $icon = config('document-icons.word');

        } else if($extension == 'xlsx' || $extension == 'csv'){
            
            $icon = config('document-icons.excel');

        } else if($extension == 'pdf'){
            
            $icon = config('document-icons.pdf');

        } else if ($extension === 'ppt' || $extension === 'pptx'){
            
            $icon = config('document-icons.ppt');

        } else if ($extension == 'gif'){
            
            $icon = config('document-icons.gif');
            
        }
        else {
            $icon = config('document-icons.default');
        }

        return [
            'id' => $this->id,
            'date' => $this->date,
            'document_date' => $this->document_date,
            'name' => $this->name,
            'document_file' => $documentUrl,
            'scale' => $this->scale,
            'request_status' => $this->request_status,
            'last_edited' => $this->last_edited,
            'signed_date' => $this->signed_date,
            'designer_in_charge_id' => $this->designer_in_charge_id,
            'project_id' => $this->project_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'icon' => $icon,
            'extension' => $extension,
        ];
    }
}
