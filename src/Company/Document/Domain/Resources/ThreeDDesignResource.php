<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ThreeDDesignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $documentUrl = $this->document_file ? asset('storage/3d_design/' . $this->document_file) : null;

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
            'name' => $this->name,
            'date' => $this->date,
            'last_edited' => $this->last_edited,
            'document_file' => $documentUrl,
            'icon' => $icon,
            'extension' => $extension
        ];
    }
}
