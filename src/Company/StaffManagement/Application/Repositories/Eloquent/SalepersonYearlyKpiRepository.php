<?php

namespace Src\Company\StaffManagement\Application\Repositories\Eloquent;

use Src\Company\StaffManagement\Infrastructure\EloquentModels\SalepersonYearlyKpiEloquentModel;
use Src\Company\StaffManagement\Application\DTO\SalepersonYearlyKpiData;
use Src\Company\StaffManagement\Application\Mappers\SalepersonYearlyKpiMapper;
use Src\Company\StaffManagement\Domain\Model\Entities\SalepersonYearlyKpi;
use Src\Company\StaffManagement\Domain\Repositories\SalepersonYearlyKpiRepositoryInterface;

class SalepersonYearlyKpiRepository implements SalepersonYearlyKpiRepositoryInterface
{

    public function getKpiRecords($saleperson_id)
    {
        //saleperson kpi list
        
        $salepersonKpiEloquent = SalepersonYearlyKpiEloquentModel::query()->where('saleperson_id',$saleperson_id)->get();
        
        return $salepersonKpiEloquent;
    }

    public function getKpiRecordsByYear($saleperson_id,$year)
    {
        $salepersonKpiEloquent = SalepersonYearlyKpiEloquentModel::query()->where('saleperson_id',$saleperson_id)->where('year',$year)->first();
        
        return $salepersonKpiEloquent;
    }

    public function store(SalepersonYearlyKpi $salepersonKpi): SalepersonYearlyKpiData
    {
        $salepersonKpiEloquent = SalepersonYearlyKpiMapper::toEloquent($salepersonKpi);

        $salepersonKpiEloquent->save();

        return SalepersonYearlyKpiData::fromEloquent($salepersonKpiEloquent);
    }

}