<?php

namespace Src\Company\StaffManagement\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Src\Company\StaffManagement\Application\Mappers\StaffMapper;
use Src\Company\StaffManagement\Domain\Model\Staff;
use Src\Company\StaffManagement\Domain\Repositories\StaffRepositoryInterface;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;

class StaffRepository implements StaffRepositoryInterface
{
    public function store(Staff $staff)
    {
        $staffEloquent = StaffMapper::toEloquent($staff);
        $staffEloquent->save();

        return $staffEloquent;
    }

    public function update(Staff $staff)
    {
        $staffEloquent = StaffEloquentModel::updateOrCreate(
            ['user_id' => $staff->user_id], // Conditions to match
            ['rank_id' => $staff->rank_id, 'mgr_id' => $staff->mgr_id, 'registry_no' => $staff->registry_no, 'rank_updated_at' => Carbon::now()] // Values to update or create with
        );

        return $staffEloquent;
    }
}
