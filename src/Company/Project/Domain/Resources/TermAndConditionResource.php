<?php

namespace Src\Company\Project\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TermAndConditionResource extends JsonResource
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
            'contents' => TermAndConditionPagesResource::collection($this->pages)
        ];
    }
}
