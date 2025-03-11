<?php

namespace Src\Company\CustomerManagement\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\CustomerManagement\Domain\Model\Entities\IdMilestone;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\IdMilestonesEloquentModel;

class IdMilestoneMapper
{
    public static function fromRequest(Request $request, ?int $id_milestone_id = null): IdMilestone
    {
        return new IdMilestone(
            id: $id_milestone_id,
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

    public static function fromEloquent(IdMilestonesEloquentModel $idMilestoneEloquent): IdMilestone
    {
        return new IdMilestone(
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

    public static function toEloquent(IdMilestone $id_milestone): IdMilestonesEloquentModel
    {
        $idMilestoneEloquent = new IdMilestonesEloquentModel();
        $last_index = 1;
        if ($id_milestone->id) {
            $idMilestoneEloquent = IdMilestonesEloquentModel::query()->findOrFail($id_milestone->id);
            $last_index = $idMilestoneEloquent->index;
        }else{
            $last_item = IdMilestonesEloquentModel::orderBy('index','desc')->first();
            if($last_item){
                $last_index = $last_item->index + 1;
            }
        }
        $idMilestoneEloquent->name = $id_milestone->name;
        $idMilestoneEloquent->message_type = $id_milestone->message_type;
        $idMilestoneEloquent->role = $id_milestone->role;
        $idMilestoneEloquent->duration = $id_milestone->duration;
        $idMilestoneEloquent->index = $last_index;
        $idMilestoneEloquent->color_code = $id_milestone->color_code;
        $idMilestoneEloquent->status = $id_milestone->status;
        $idMilestoneEloquent->action = $id_milestone->action;
        $idMilestoneEloquent->whatsapp_template = $id_milestone->whatsapp_template;
        $idMilestoneEloquent->whatsapp_language = $id_milestone->whatsapp_language;
        $idMilestoneEloquent->whatsapp_template_reminder = $id_milestone->whatsapp_template_reminder;
        $idMilestoneEloquent->whatsapp_language_reminder = $id_milestone->whatsapp_language_reminder;
        $idMilestoneEloquent->title = $id_milestone->title;
        $idMilestoneEloquent->message = $id_milestone->message;

        return $idMilestoneEloquent;
    }
}
