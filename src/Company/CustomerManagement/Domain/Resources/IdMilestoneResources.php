<?php

namespace Src\Company\CustomerManagement\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IdMilestoneResources extends JsonResource
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
            'name' => $this->name,
            'message_type' => $this->message_type,
            'role' => $this->role,
            'duration' => $this->duration,
            'index' => $this->index,
            'color_code' => $this->color_code,
            'status' => $this->status,
            'action' => $this->action,
            'whatsapp_template' => $this->whatsapp_template,
            'whatsapp_language' => $this->whatsapp_language,
            'whatsapp_template_reminder' => $this->whatsapp_template_reminder,
            'whatsapp_language_reminder' => $this->whatsapp_language_reminder,
            'title' => $this->title,
            'message' => $this->message,
            'transitions' => $this->fromTransitions
        ];
    }
}
