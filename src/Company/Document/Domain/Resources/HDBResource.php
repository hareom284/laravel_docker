<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HDBResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $documentUrl = $this->document_file ? asset('storage/hdb_forms_file/' . $this->document_file) : null;

        $extension = strtolower(pathinfo($documentUrl, PATHINFO_EXTENSION));

        if($extension == 'svg' || $extension == 'jpg' || $extension == 'png' || $extension == 'JPEG' || $extension == 'jpeg'){
            
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
            'project_id' => $this->project_id,
            'name' => $this->name,
            'date_uploaded' => $this->date_uploaded,
            'document_file' => $this->document_file,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'documentUrl' => $documentUrl,
            'icon' => $icon,
            'extension' => $extension
        ];
    }
}
