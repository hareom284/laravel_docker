<?php

namespace Src\Company\CustomerManagement\Application\Mappers;

use Illuminate\Http\Request;
use Src\Company\CustomerManagement\Domain\Model\Entities\CheckList;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CheckListEloquentModel;

class CheckListMapper
{
    public static function fromRequest(Request $request, ?int $check_list_id = null): CheckList
    {

        return new CheckList(
            id: $check_list_id,
            description: $request->string('description'),
            is_completed: $request->integer('is_completed'),
            customer_id:$request->integer('customer_id'),
        );
    }

    public static function fromEloquent(CheckListEloquentModel $checkListEloquent): CheckList
    {
        return new CheckList(
            id: $checkListEloquent->id,
            description: $checkListEloquent->description,
            is_completed: $checkListEloquent->is_completed,
            customer_id: $checkListEloquent->customer_id,
        );
    }

    public static function toEloquent(CheckList $check_list): CheckListEloquentModel
    {
        $checkListEloquent = new CheckListEloquentModel();
        if ($check_list->id) {
            $checkListEloquent = CheckListEloquentModel::query()->findOrFail($check_list->id);
        }
        $checkListEloquent->description = $check_list->description;
        $checkListEloquent->is_completed = $check_list->is_completed;
        $checkListEloquent->customer_id = $check_list->customer_id;
        return $checkListEloquent;
    }
}
