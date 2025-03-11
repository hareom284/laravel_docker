<?php

namespace Src\Company\Project\Application\DTO;

use Illuminate\Http\Request;
use PhpParser\Node\Expr\Cast\Double;
use Src\Company\Project\Infrastructure\EloquentModels\RenovationItemScheduleEloquentModel;

class RenovationItemScheduleData
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $project_id,
        public readonly array $renovation_item_id,
        public readonly array $start_date,
        public readonly array $end_date,
        public readonly array $show_in_timeline
    )
    {}

    public static function fromRequest(Request $request, ?int $schedule_id = null): RenovationItemScheduleData
    {
        return new self(
            id: $schedule_id,
            project_id: $request->integer('project_id'),
            renovation_item_id: $request->integer('renovation_item_id'),
            start_date: $request->string('start_date'),
            end_date: $request->string('end_date'),
            show_in_timeline: $request->boolean('show_in_timeline')
        );
    }

    public static function fromEloquent(RenovationItemScheduleEloquentModel $scheduleEloquent): self
    {
        return new self(
            id: $scheduleEloquent->id,
            project_id: $scheduleEloquent->project_id,
            renovation_item_id: $scheduleEloquent->renovation_item_id,
            start_date: $scheduleEloquent->start_date,
            end_date: $scheduleEloquent->end_date,
            show_in_timeline: $scheduleEloquent->show_in_timeline
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'renovation_item_id' => $this->renovation_item_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'show_in_timeline' => $this->show_in_timeline
        ];
    }
}