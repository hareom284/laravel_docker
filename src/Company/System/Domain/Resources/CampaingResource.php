<?php

namespace Src\Company\System\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $total_sent = $this->campaignAudiences()->count();
        $read = $this->campaignAudiences()->whereNotNull('read_at')->count();
        $unread = $this->campaignAudiences()->whereNull('read_at')->count();
        return [
            'id' => $this->id,
            'title' => $this->title,
            'campaignAudiences' => $this->campaignAudiences,
            'total_sent' => $total_sent,
            'read' => $read,
            'unread' => $unread
        ];
    }
}
