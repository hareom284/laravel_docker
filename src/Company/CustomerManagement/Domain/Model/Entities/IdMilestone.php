<?php

namespace Src\Company\CustomerManagement\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class IdMilestone extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $name,
        public readonly ?string $message_type,
        public readonly ?string $role,
        public readonly ?int $duration,
        public readonly ?int $index,
        public readonly ?string $color_code,
        public readonly ?string $status,
        public readonly ?string $action,
        public readonly ?string $whatsapp_template,
        public readonly ?string $whatsapp_language,
        public readonly ?string $whatsapp_template_reminder,
        public readonly ?string $whatsapp_language_reminder,
        public readonly ?string $title,
        public readonly ?string $message,
        public readonly ?string $transitions
    ) {}



    public function toArray(): array
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
            'whatsapp_template' => $this->whatsapp_template_reminder,
            'whatsapp_language' => $this->whatsapp_language_reminder,
            'title' => $this->title,
            'message' => $this->message
        ];
    }
}
