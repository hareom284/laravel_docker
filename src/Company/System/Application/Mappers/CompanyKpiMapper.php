<?php

namespace Src\Company\System\Application\Mappers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Src\Company\System\Infrastructure\EloquentModels\KpiRecordEloquentModel;
use Src\Company\System\Domain\Model\Entities\CompanyKpi;

class CompanyKpiMapper
{
    public static function fromRequest(Request $request, ?int $kpi_id = null): CompanyKpi
    {

        return new CompanyKpi(
            id: $kpi_id,
            type: $request->string('type'),
            period: $request->string('period'),
            target: $request->string('target'),
            company_id: $request->integer('company_id'),
        );
    }

    public static function fromEloquent(KpiRecordEloquentModel $kpiRecordEloquentModel): CompanyKpi
    {
        return new CompanyKpi(
            id: $kpiRecordEloquentModel->id,
            type: $kpiRecordEloquentModel->type,
            period: $kpiRecordEloquentModel->period,
            target: $kpiRecordEloquentModel->target,
            company_id: $kpiRecordEloquentModel->company_id,
        );
    }

    public static function toEloquent(CompanyKpi $companyKpi): KpiRecordEloquentModel
    {
        $kpiEloquent = new KpiRecordEloquentModel();

        if ($companyKpi->id) {
            $kpiEloquent = KpiRecordEloquentModel::query()->where('id',$companyKpi->id)->first();
        }

        $kpiEloquent->type = $companyKpi->type;
        $kpiEloquent->period = $companyKpi->period;
        $kpiEloquent->target = $companyKpi->target;
        $kpiEloquent->company_id = $companyKpi->company_id;

        return $kpiEloquent;
    }
}