<?php

namespace Src\Company\CustomerManagement\Application\Repositories\Eloquent;

use Src\Company\CustomerManagement\Application\Mappers\CheckListMapper;
use Src\Company\CustomerManagement\Domain\Model\Entities\CheckList;
use Src\Company\CustomerManagement\Domain\Repositories\CheckListRepositoryInterface;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CheckListEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;

class CheckListRepository implements CheckListRepositoryInterface
{
    public function checkListByCustomerId($id)
    {
        $customer_info = CustomerEloquentModel::query()->where('user_id',$id)->first();

        $checklists = CheckListEloquentModel::query()->where('customer_id',$customer_info->id)->get();

        return $checklists;

    }

    public function store(CheckList $checkList)
    {
        $checkEloquent = CheckListMapper::toEloquent($checkList);

        $checkEloquent->save();

        return CheckListMapper::fromEloquent($checkEloquent);
    }

    public function delete(int $checklist_id)
    {
        $checkListEloquent = CheckListEloquentModel::query()->findOrFail($checklist_id);
        $checkListEloquent->delete();
    }

    public function completeCheckList(CheckList $check_list,int $checklist_id)
    {

        $checkData = CheckListMapper::toEloquent($check_list);

        $checkListEloquent = CheckListEloquentModel::query()->findOrFail($checklist_id);
        $checkListEloquent->is_completed = $checkData->is_completed;
        $checkListEloquent->save();

        return CheckListMapper::fromEloquent($checkListEloquent);
    }
}
