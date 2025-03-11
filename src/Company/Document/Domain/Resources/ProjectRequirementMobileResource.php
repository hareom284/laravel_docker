<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProjectRequirementMobileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $filePath = $this->document_file ? 'storage/project_requirement/'. $this->document_file : null;

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        $documentUrl = asset($filePath);
        
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
            'title' => $this->title,
            'document_file' => $documentUrl,
            'original_document' => $this->document_file,
            'project_id' => $this->project_id,
            'extension' => $extension,
            'icon' => $icon
        ];
    }
}
