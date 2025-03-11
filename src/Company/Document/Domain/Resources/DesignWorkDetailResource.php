<?php

namespace Src\Company\Document\Domain\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignWorkDetailResource extends JsonResource
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
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
            'document_date' => $this->document_date,
            'document_date' => $this->document_date,
            'name' => $this->name,
            'project_id' => $this->project_id,
            'request_status' => $this->request_status,
            'scale' => $this->scale,
            'signature' => $this->signature,
            'signed_date' => $this->signed_date,
            'document_file' =>  $documentUrl,
            'original_document' => $this->document_file,
            'scale' => $this->scale,
            'request_status' => $this->request_status,
            'last_edited' => $this->last_edited,
            'designer' => $this->designer,
            'assistant_designer' => $this->assistantDesigner,
            'materials' => $this->materials,
            'project' => $this->project,
            'icon' => $icon,
            'extension' => $extension
        ];
    }
}
