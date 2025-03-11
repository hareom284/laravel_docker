<?php

namespace Src\Company\Project\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\Project\Domain\Model\Entities\TermAndCondition;
use Src\Company\Project\Infrastructure\EloquentModels\TermAndConditionEloquentModel;

class TermAndConditionMapper
{
    public static function fromRequest(Request $request, ?int $termAndCondition_id = null): TermAndCondition
    {
        return new TermAndCondition(
            id: $termAndCondition_id,
            title: $request->string('title'),
        );
    }

    public static function fromEloquent(TermAndConditionEloquentModel $termAndConditionEloquent): TermAndCondition
    {
        return new TermAndCondition(
            id: $termAndConditionEloquent->id,
            title: $termAndConditionEloquent->title,
        );
    }

    public static function toEloquent(TermAndCondition $termAndCondition): TermAndConditionEloquentModel
    {
        $termAndConditionEloquent = new TermAndConditionEloquentModel();
        if ($termAndCondition->id) {
            $termAndConditionEloquent = TermAndConditionEloquentModel::query()->findOrFail($termAndCondition->id);
        }
        $termAndConditionEloquent->title = $termAndCondition->title;
        return $termAndConditionEloquent;
    }
}
