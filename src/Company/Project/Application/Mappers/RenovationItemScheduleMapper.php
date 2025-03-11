<?php

namespace Src\Company\Project\Application\Mappers;

use Illuminate\Http\Request;
use Src\Company\Project\Domain\Model\Entities\RenovationItemSchedule;
use Src\Company\Project\Infrastructure\EloquentModels\RenovationItemScheduleEloquentModel;
use Src\Company\Project\Application\DTO\RenovationItemScheduleData;

class RenovationItemScheduleMapper {
    
    public static function fromRequest(Request $request, ?int $schedule_id = null): RenovationItemSchedule
    {
        return new RenovationItemSchedule(
            id: $schedule_id,
            project_id: $request->integer('project_id'),
            renovation_item_id: $request->input('renovation_item_id'),
            start_date: $request->input('start_date'),
            end_date: $request->input('end_date'),
            show_in_timeline: $request->input('show_in_timeline')
        );
    }

    public static function fromEloquent(RenovationItemScheduleEloquentModel $scheduleEloquent): RenovationItemScheduleData
    {
        return new RenovationItemScheduleData(
            id: $scheduleEloquent->id,
            project_id: $scheduleEloquent->project_id,
            renovation_item_id: $scheduleEloquent->renovation_item_id,
            start_date: $scheduleEloquent->start_date,
            end_date: $scheduleEloquent->end_date,
            show_in_timeline: $scheduleEloquent->show_in_timeline,
        );
    }

    public static function toEloquent(RenovationItemSchedule $schedule): RenovationItemScheduleEloquentModel
    {
        $scheduleEloquent = new RenovationItemScheduleEloquentModel();
        if($schedule->id)
        {
            $scheduleEloquent = RenovationItemScheduleEloquentModel::query()->findOrFail($schedule->id);
        }
        $scheduleEloquent->project_id = $schedule->project_id;
        $scheduleEloquent->renovation_item_id = $schedule->renovation_item_id;
        $scheduleEloquent->start_date = $schedule->start_date;
        $scheduleEloquent->end_date = $schedule->end_date;
        $scheduleEloquent->show_in_timeline = $schedule->show_in_timeline;

        return $scheduleEloquent;
    }

}