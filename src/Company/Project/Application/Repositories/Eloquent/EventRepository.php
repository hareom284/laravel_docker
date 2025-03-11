<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Company\Project\Application\DTO\EventData;
use Src\Company\Project\Application\Mappers\EventMapper;
use Src\Company\Project\Domain\Model\Entities\Event;
use Src\Company\Project\Domain\Resources\EventResource;
use Src\Company\Project\Domain\Repositories\EventRepositoryInterface;
use Src\Company\Project\Infrastructure\EloquentModels\EventEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\EventCommentEloquentModel;

class EventRepository implements EventRepositoryInterface
{

    public function getEvents()
    {
        $authStaffId = auth('sanctum')->user()->id;

        $eventEloquent = EventEloquentModel::where('staff_id',$authStaffId)->orderBy('id', 'desc')->get();
        
        return $eventEloquent;
    }

    public function getEventsByProjectId(int $projectId)
    {
        $authStaffId = auth('sanctum')->user()->id;
        
        $eventEloquent = EventEloquentModel::query()->where('project_id',$projectId)->where('staff_id',$authStaffId)->orderBy('id', 'desc')->get();

        return $eventEloquent;
    }

    public function getEventsByGroup()
    {
        $authStaffId = auth('sanctum')->user()->id;
        
        $eventEloquent = EventEloquentModel::query()->where('staff_id',$authStaffId)->orderBy('id', 'desc')->get()->groupBy('status');
        
        return $eventEloquent;
    }

    // public function getEventsByGroup()
    // {
    //     $authStaffId = auth('sanctum')->user()->id;
        
    //     $eventEloquent = EventEloquentModel::query()->where('staff_id',$authStaffId)->orderBy('id', 'desc')->get()->groupBy('status');
        
    //     return $eventEloquent;
    // }

    public function findById(int $id): EventData
    {
        $eventEloquent = EventEloquentModel::query()->findOrFail($id);

        return EventData::fromEloquent($eventEloquent, true, true, true);
    }

    public function store(Event $event,$eventComments): EventData
    {

        $eventEloquent = EventMapper::toEloquent($event);

        $eventEloquent->save();

        foreach ($eventComments as $comment) {

            // EventCommentEloquentModel::create([
            //     'description' => $comment,
            //     'event_id' => $eventEloquent->id,
            // ]);
            EventCommentEloquentModel::create([
                'description' => $comment['description'],
                'is_completed' => $comment['checked'],
                'event_id' => $eventEloquent->id,
            ]);
        }

        return EventData::fromEloquent($eventEloquent);

        // return DB::transaction(function () use ($event,$eventComments) {

        //     $eventEloquent = EventMapper::toEloquent($event);

        //     $eventEloquent->save();

        //     $eventEloquent->comments()->attach($eventComments);

        //     return EventData::fromEloquent($eventEloquent);
        // });
    }
    public function update(Event $event,$eventComments): EventData
    {
        $eventEloquent = EventMapper::toEloquent($event);

        $eventEloquent->save();

        foreach ($eventComments as $comment)
        {
            if(isset($comment['id']))
            {
                EventCommentEloquentModel::query()->find($comment['id'])->update([
                    'description' => $comment['description'],
                    'is_completed' => $comment['is_completed'],
                ]);
            } else {
                EventCommentEloquentModel::create([
                    'description' => $comment['description'],
                    'is_completed' => $comment['checked'],
                    'event_id' => $eventEloquent->id,
                ]);
            }
        }

        return EventData::fromEloquent($eventEloquent);
    }

    public function changeStatus(int $id,string $status): EventData
    {
        $eventEloquent = EventEloquentModel::query()->findOrFail($id);

        $eventEloquent->status = $status;

        $eventEloquent->save();

        return EventData::fromEloquent($eventEloquent);
    }

    public function eventCommentsByEventId(int $id)
    {
        $eventCommentEloquent = EventCommentEloquentModel::query()->where('event_id',$id)->get();

        return $eventCommentEloquent;
    }

    public function delete(int $event_id): void
    {
        $eventEloquent = EventEloquentModel::query()->findOrFail($event_id);
        $eventEloquent->delete();
    }
}