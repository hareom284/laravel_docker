<?php

namespace Src\Company\Project\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\Project\Domain\Model\Entities\Event;
use Src\Company\Project\Infrastructure\EloquentModels\EventEloquentModel;

class EventMapper
{
    public static function fromRequest(Request $request, ?int $event_id = null): Event
    {
        $userId = auth('sanctum')->user()->id;

        return new Event(
            id: $event_id,
            title: $request->string('title'),
            description: $request->string('description') ?? "",
            start_date: $request->string('start_date') ?? "",
            end_date: $request->string('end_date') ?? "",
            status: $request->string('status') ?? "",
            staff_id: $userId,
            project_id: $request->integer('project_id') ?? ""
        );
    }

    public static function fromEloquent(EventEloquentModel $eventEloquent): Event
    {
        return new Event(
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

    public static function toEloquent(Event $event): EventEloquentModel
    {
        $eventEloquent = new EventEloquentModel();
        if ($event->id) {
            $eventEloquent = EventEloquentModel::query()->findOrFail($event->id);
        }
        $eventEloquent->title = $event->title;
        $eventEloquent->description = $event->description;
        $eventEloquent->start_date = $event->start_date;
        $eventEloquent->end_date = $event->end_date;
        $eventEloquent->status = $event->status;
        $eventEloquent->staff_id = $event->staff_id;
        $eventEloquent->project_id = $event->project_id;
        return $eventEloquent;
    }
}