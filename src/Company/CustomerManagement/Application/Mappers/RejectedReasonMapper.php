<?php

namespace Src\Company\CustomerManagement\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\CustomerManagement\Domain\Model\Entities\RejectedReason;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\RejectedReasonsEloquentModel;

class RejectedReasonMapper
{
    public static function fromRequest(Request $request, ?int $rejected_reason_id = null): RejectedReason
    {
        return new RejectedReason(
            id: $rejected_reason_id,
            name: $request->string('name'),
            index: $request->integer('index'),
            color_code: $request->string('color_code')
        );
    }

    public static function fromEloquent(RejectedReasonsEloquentModel $rejectedReasonEloquent): RejectedReason
    {
        return new RejectedReason(
            id: $rejectedReasonEloquent->id,
            name: $rejectedReasonEloquent->name,
            index: $rejectedReasonEloquent->index,
            color_code: $rejectedReasonEloquent->color_code
        );
    }

    public static function toEloquent(RejectedReason $rejected_reason): RejectedReasonsEloquentModel
    {
        $rejectedReasonEloquent = new RejectedReasonsEloquentModel();
        $last_index=1;
        if ($rejected_reason->id) {
            $rejectedReasonEloquent = RejectedReasonsEloquentModel::query()->findOrFail($rejected_reason->id);
            $last_index = $rejectedReasonEloquent->index;
        }else{
            $last_item = RejectedReasonsEloquentModel::orderBy('index','desc')->first();
            if($last_item){
                $last_index = $last_item->index + 1;
            }
        }
        $rejectedReasonEloquent->name = $rejected_reason->name;
        $rejectedReasonEloquent->index = $last_index;
        $rejectedReasonEloquent->color_code = $rejected_reason->color_code;
        return $rejectedReasonEloquent;
    }
}
