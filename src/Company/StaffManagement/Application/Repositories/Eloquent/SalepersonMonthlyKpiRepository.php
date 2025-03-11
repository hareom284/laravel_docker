<?php

namespace Src\Company\StaffManagement\Application\Repositories\Eloquent;

use Src\Company\StaffManagement\Application\DTO\SalepersonMonthlyKpiData;
use Src\Company\StaffManagement\Application\Mappers\SalepersonMonthlyKpiMapper;
use Src\Company\StaffManagement\Domain\Model\Entities\SalepersonMonthlyKpi;
use Src\Company\StaffManagement\Domain\Repositories\SalepersonMonthlyKpiRepositoryInterface;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\SalepersonMonthlyKpiEloquentModel;

class SalepersonMonthlyKpiRepository implements SalepersonMonthlyKpiRepositoryInterface
{

    public function getKpiRecords($saleperson_id)
    {
        //saleperson kpi list
        
        $salepersonKpiEloquent = SalepersonMonthlyKpiEloquentModel::query()->where('saleperson_id',$saleperson_id)->get();
        
        return $salepersonKpiEloquent;
    }

    public function getKpiRecordsByMonth($saleperson_id,$year,$month)
    {
        $salepersonKpiEloquent = SalepersonMonthlyKpiEloquentModel::query()->where('saleperson_id',$saleperson_id)->where('year',$year)->where('month',$month)->first();
        
        return $salepersonKpiEloquent;
    }

    public function store(SalepersonMonthlyKpi $salepersonKpi): SalepersonMonthlyKpiData
    {
        $salepersonKpiEloquent = SalepersonMonthlyKpiMapper::toEloquent($salepersonKpi);

        $salepersonKpiEloquent->save();

        return SalepersonMonthlyKpiData::fromEloquent($salepersonKpiEloquent);
    }

}