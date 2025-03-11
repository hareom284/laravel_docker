<?php

namespace Src\Company\Project\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Project\Infrastructure\EloquentModels\EventEloquentModel;

class EventData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly string $description,
        public readonly string $start_date,
        public readonly string $end_date,
        public readonly string $status,
        public readonly int $staff_id,
        public readonly int $project_id
    )
    {}

    public static function fromRequest(Request $request, ?int $event_id = null): EventData
    {
        return new self(
            id: $event_id,
            title: $request->string('title'),
            description: $request->string('description'),
            start_date: $request->string('start_date'),
            end_date: $request->string('end_date'),
            status: $request->string('status'),
            staff_id: $request->integer('staff_id'),
            project_id: $request->integer('project_id')
        );
    }

    public static function fromEloquent(EventEloquentModel $eventEloquent): self
    {
        return new self(
            id: $eventEloquent->id,
            title: $eventEloquent->title,
            description: $eventEloquent->description,
            start_date: $eventEloquent->start_date,
            end_date: $eventEloquent->end_date,
            status: $eventEloquent->status,
            staff_id: $eventEloquent->staff_id,
            project_id: $eventEloquent->project_id
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'staff_id' => $this->staff_id,
            'project_id' => $this->project_id
        ];
    }
}