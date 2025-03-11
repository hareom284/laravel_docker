<?php

namespace Src\Company\CustomerManagement\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\IdMilestonesEloquentModel;

class IdMilestoneData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
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
    )
    {}

    public static function fromRequest(Request $request, ?int $company_id = null): IdMilestoneData
    {
        return new self(
            id: $company_id,
            name: $request->string('name'),
            message_type: $request->string('message_type'),
            role: $request->string('role'),
            duration: $request->integer('duration'),
            index: $request->integer('index'),
            color_code: $request->string('color_code'),
            status: $request->string('status'),
            action: $request->string('action'),
            whatsapp_template: $request->string('whatsapp_template'),
            whatsapp_language: $request->string('whatsapp_language'),
            whatsapp_template_reminder: $request->string('whatsapp_template_reminder'),
            whatsapp_language_reminder: $request->string('whatsapp_language_reminder'),
            title: $request->string('title'),
            message: $request->string('message'),
            transitions: $request->string('transitions')
        );
    }

    public static function fromEloquent(IdMilestonesEloquentModel $idMilestoneEloquent): self
    {
        return new self(
            id: $idMilestoneEloquent->id,
            name: $idMilestoneEloquent->name,
            message_type: $idMilestoneEloquent->message_type,
            role: $idMilestoneEloquent->role,
            duration: $idMilestoneEloquent->duration,
            index: $idMilestoneEloquent->index,
            color_code: $idMilestoneEloquent->color_code,
            status: $idMilestoneEloquent->status,
            action: $idMilestoneEloquent->action,
            whatsapp_template: $idMilestoneEloquent->whatsapp_template,
            whatsapp_language: $idMilestoneEloquent->whatsapp_language,
            whatsapp_template_reminder: $idMilestoneEloquent->whatsapp_template_reminder,
            whatsapp_language_reminder: $idMilestoneEloquent->whatsapp_language_reminder,
            title: $idMilestoneEloquent->title,
            message: $idMilestoneEloquent->message,
            transitions: $idMilestoneEloquent->fromTransitions
        );
    }

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
            'whatsapp_template_reminder' => $this->whatsapp_template_reminder,
            'whatsapp_language_reminder' => $this->whatsapp_language_reminder,
            'title' => $this->title,
            'message' => $this->message
        ];
    }
}
