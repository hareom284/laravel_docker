<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $documentUrl = $this->document_file ? asset('storage/document_file/' . $this->document_file) : null;

        if($this->file_type == 'svg' || $this->file_type == 'jpg' || $this->file_type == 'png' || $this->file_type == 'JPEG' || $this->file_type == 'jpeg'){
            
            $icon = config('document-icons.image');

        } else if($this->file_type == 'docx'){
            
            $icon = config('document-icons.word');

        } else if($this->file_type == 'xlsx' || $this->file_type == 'csv'){
            
            $icon = config('document-icons.excel');

        } else if($this->file_type == 'pdf'){
            
            $icon = config('document-icons.pdf');

        } else if ($this->file_type === 'ppt' || $this->file_type === 'pptx'){
            
            $icon = config('document-icons.ppt');

        } else if ($this->file_type == 'gif'){
            
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
            'file_type' => $this->file_type,
            'allow_customer_view' => $this->allow_customer_view == 1 ? true : false,
            'folder_id' => $this->folder_id,
            'project_id' => $this->project_id,
            'folder' => $this->folder,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'icon' => $icon
        ];
    }
}
