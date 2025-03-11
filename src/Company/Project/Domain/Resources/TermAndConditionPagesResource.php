<?php

namespace Src\Company\Project\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TermAndConditionPagesResource extends JsonResource
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
            'page_number' => $this->page_number,
            'paragraphs' => TermAndConditionParagraphResource::collection($this->paragraphs) 
        ];
    }
}
