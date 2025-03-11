<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FolderResource extends JsonResource
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
            'title' => $this->title,
            'allow_customer_view' => $this->allow_customer_view == 1 ? true : false,
            'project_id' => $this->project_id,
            'documents' => DocumentResource::collection($this->documents)
        ];
    }
}
