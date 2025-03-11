<?php

namespace Src\Company\System\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Src\Company\System\Application\DTO\CompanyData;
use Src\Company\System\Application\Mappers\CompanyMapper;
use Src\Company\System\Domain\Model\Entities\Company;
use Src\Company\System\Domain\Resources\CompanyResource;
use Src\Company\System\Domain\Repositories\CompanyRepositoryInterface;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentStandardEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\KpiRecordEloquentModel;
use Src\Company\System\Application\DTO\CompanyKpiData;
use Src\Company\System\Application\Mappers\CompanyKpiMapper;
use Src\Company\System\Domain\Model\Entities\CompanyKpi;
use Src\Company\System\Domain\Repositories\CompanyKpiRepositoryInterface;

class CompanyKpiRepository implements CompanyKpiRepositoryInterface
{

    public function getKpiRecords($company_id)
    {
        //companies kpi list
        
        $companyKpiEloquent = KpiRecordEloquentModel::query()->where('company_id',$company_id)->get();

        $saleReports = SaleReportEloquentModel::whereHas('project', function ($query) use ($company_id) {
            $query->where('company_id', $company_id);
        })
        ->where('total_sales', '!=' , 0)
        ->where('total_sales', '!=' , NULL)
        ->with('project')
        ->get();

        $yearData = $saleReports->groupBy(function ($item) {
            return Carbon::parse($item->created_at)->format('Y');
        })->map(function ($groupedItems) {
            $total = 0;
            $data = $groupedItems->map(function ($item) use (&$total) {
                $total += $item->total_sales;
            });

            return [
                'actual_revenue' => $total,
                // 'data' => $data,
            ];
        });

        // Iterate through the first array
        foreach ($companyKpiEloquent as &$item) {
            // Check if the 'period' key exists in the actualRevenueData array
            if(count($saleReports) > 0){
                if (isset($yearData[$item['period']])) {
                    // Add the 'actual_revenue' data to the current item
                    $item['actual_revenue'] = $yearData[$item['period']]['actual_revenue'];
                }
            } else {
                $item['actual_revenue'] = 0.00;
            }
        }
        
        return $companyKpiEloquent;
    }

    public function getKpiRecordsByYear($company_id,$year)
    {
        $companyKpiEloquent = KpiRecordEloquentModel::query()->where('company_id',$company_id)->where('period',$year)->first();
        
        return $companyKpiEloquent;
    }

    public function store(CompanyKpi $companyKpi): CompanyKpiData
    {
        $companyKpiEloquent = CompanyKpiMapper::toEloquent($companyKpi);

        $companyKpiEloquent->save();

        return CompanyKpiData::fromEloquent($companyKpiEloquent);
    }

}