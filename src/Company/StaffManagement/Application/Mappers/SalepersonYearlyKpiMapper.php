<?php

namespace Src\Company\StaffManagement\Application\Mappers;

use Illuminate\Http\Request;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\SalepersonYearlyKpiEloquentModel;
use Src\Company\StaffManagement\Domain\Model\Entities\SalepersonYearlyKpi;

class SalepersonYearlyKpiMapper
{
    public static function fromRequest(Request $request, ?int $kpi_id = null, int $salepersonId): SalepersonYearlyKpi
    {

        return new SalepersonYearlyKpi(
            id: $kpi_id,
            year: $request->string('year'),
            management_target: $request->string('management_target'),
            saleperson_id: $salepersonId,
        );
    }

    public static function fromEloquent(SalepersonYearlyKpiEloquentModel $yearlyKpiEloquentModel): SalepersonYearlyKpi
    {
        return new SalepersonYearlyKpi(
            id: $yearlyKpiEloquentModel->id,
            year: $yearlyKpiEloquentModel->year,
            management_target: $yearlyKpiEloquentModel->management_target,
            saleperson_id: $yearlyKpiEloquentModel->saleperson_id,
        );
    }

    public static function toEloquent(SalepersonYearlyKpi $salepersonKpi): SalepersonYearlyKpiEloquentModel
    {
        $salepersonKpiEloquent = new SalepersonYearlyKpiEloquentModel();

        if ($salepersonKpi->id) {
            $salepersonKpiEloquent = SalepersonYearlyKpiEloquentModel::query()->where('id',$salepersonKpi->id)->first();
        }

        $salepersonKpiEloquent->year = $salepersonKpi->year;
        $salepersonKpiEloquent->management_target = $salepersonKpi->management_target;
        $salepersonKpiEloquent->saleperson_id = $salepersonKpi->saleperson_id;

        return $salepersonKpiEloquent;
    }
}