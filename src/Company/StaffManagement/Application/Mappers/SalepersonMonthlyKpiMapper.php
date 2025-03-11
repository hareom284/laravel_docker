<?php

namespace Src\Company\StaffManagement\Application\Mappers;

use Illuminate\Http\Request;
use Src\Company\StaffManagement\Domain\Model\Entities\SalepersonMonthlyKpi;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\SalepersonMonthlyKpiEloquentModel;

class SalepersonMonthlyKpiMapper
{
    public static function fromRequest(Request $request, ?int $kpi_id = null, int $salepersonId): SalepersonMonthlyKpi
    {

        return new SalepersonMonthlyKpi(
            id: $kpi_id,
            year: $request->string('year'),
            month: $request->string('month'),
            target: $request->string('target'),
            saleperson_id: $salepersonId,
        );
    }

    public static function fromEloquent(SalepersonMonthlyKpiEloquentModel $monthlyKpiEloquentModel): SalepersonMonthlyKpi
    {
        return new SalepersonMonthlyKpi(
            id: $monthlyKpiEloquentModel->id,
            year: $monthlyKpiEloquentModel->year,
            month: $monthlyKpiEloquentModel->month,
            target: $monthlyKpiEloquentModel->target,
            saleperson_id: $monthlyKpiEloquentModel->saleperson_id,
        );
    }

    public static function toEloquent(SalepersonMonthlyKpi $salepersonKpi): SalepersonMonthlyKpiEloquentModel
    {
        $salepersonKpiEloquent = new SalepersonMonthlyKpiEloquentModel();

        if ($salepersonKpi->id) {
            $salepersonKpiEloquent = SalepersonMonthlyKpiEloquentModel::query()->where('id',$salepersonKpi->id)->first();
        }

        $salepersonKpiEloquent->year = $salepersonKpi->year;
        $salepersonKpiEloquent->month = $salepersonKpi->month;
        $salepersonKpiEloquent->target = $salepersonKpi->target;
        $salepersonKpiEloquent->saleperson_id = $salepersonKpi->saleperson_id;

        return $salepersonKpiEloquent;
    }
}