<?php

namespace Src\Company\StaffManagement\Application\Mappers;

use Illuminate\Http\Request;

use Src\Company\StaffManagement\Domain\Model\Staff;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;

class StaffMapper
{
    public static function fromRequest(Request $request, ?int $staff_id = null, ?int $user_id = null): Staff
    {

        return new Staff(
            id: $staff_id,
            user_id:$user_id,
            rank_id: $request->rank_id ? $request->integer('rank_id') : null,
            mgr_id: $request->mgr_id ?? null,
            registry_no: $request->registry_no ?? null,
            rank_updated_at: $request->rank_updated_at ?? null
        );
    }

    public static function fromEloquent(StaffEloquentModel $staffEloquent): Staff
    {
        return new Staff(
            id: $staffEloquent->id,
            user_id: $staffEloquent->user_id,
            rank_id: $staffEloquent->rank_id,
            mgr_id: $staffEloquent->mgr_id,
            registry_no: $staffEloquent->registry_no,
            rank_updated_at: $staffEloquent->rank_updated_at
        );
    }

    public static function toEloquent(Staff $staff): StaffEloquentModel
    {
        $staffEloquent = new StaffEloquentModel();

        if ($staff->id) {
            $staffEloquent = StaffEloquentModel::query()->where('id',$staff->id)->first();
        }

        $staffEloquent->user_id = $staff->user_id;
        $staffEloquent->rank_id = $staff->rank_id;
        $staffEloquent->mgr_id = $staff->mgr_id;
        $staffEloquent->registry_no = $staff->registry_no;
        $staffEloquent->rank_updated_at = $staff->rank_updated_at;
        return $staffEloquent;
    }
}
