<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Src\Company\Project\Application\DTO\SaleReportData;
use Src\Company\Project\Domain\Model\Entities\SaleReport;
use Src\Company\Project\Application\Mappers\SaleReportMapper;
use Src\Company\Document\Infrastructure\EloquentModels\EvoEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;
use Src\Company\Project\Domain\Repositories\SaleReportMobileRepositoryInterface;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SaleCommissionEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\SalepersonMonthlyKpiEloquentModel;

class SaleReportMobileRepository implements SaleReportMobileRepositoryInterface
{

    public function getSalepersonKpiReportMonth($salespersonUserId, $month = null, $year = null)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $saleperson = StaffEloquentModel::query()->where('user_id',$salespersonUserId)->first();

        $staffId = $saleperson->id;

        $salepersonKpiEloquent = SalepersonMonthlyKpiEloquentModel::query()->where('saleperson_id',$staffId)->where('year',$year)->where('month',$month)->first();

        $renoDocs = RenovationDocumentsEloquentModel::with('projects.properties','projects.salespersons')->where('signed_by_salesperson_id',$staffId)
                        ->whereNotNull('signed_date')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->get();

        $evos = EvoEloquentModel::with('projects.properties','projects.salespersons')->where('signed_by_salesperson_id',$staffId)
                ->whereNotNull('signed_date')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

        $renoTotalSales = 0;
        $evoTotalSales = $evos->sum('grand_total');

        $renoDocs->filter(function ($item) {
                    return $item->type !== "FOC"; // Filter out items with type "FOC"
                })->map(function ($item) use (&$renoTotalSales) {
                    $totalCostingAmt = round($item->total_amount, 2);
                    $renoTotalSales += ($item->type === "CANCELLATION") ? (-1 * $totalCostingAmt) : $totalCostingAmt; // Subtract for "Cancellation"
                });

        $totalSales = $renoTotalSales + $evoTotalSales;

        // Number of customers
        

        return  [
            'current_sales' => round($totalSales,2),
            'kpi' => isset($salepersonKpiEloquent) ? (float)$salepersonKpiEloquent->target : 0,
            'customers' => $saleperson->customers->where('status', 2)->count(),
        ];

    }

    public function store(int $projectId)
    {
        SaleReportEloquentModel::create([
            'project_id' => $projectId
        ]);

        return true;
    }

    public function update(SaleReport $saleReport, $saleCommissions): SaleReportData
    {

        DB::beginTransaction();

        try {
            $saleReportEloquent = SaleReportMapper::toEloquent($saleReport);

            $saleReportEloquent->save();

            if (!empty($saleCommissions)) {
                foreach ($saleCommissions as $commission) {
                    SaleCommissionEloquentModel::updateOrCreate(
                        [
                            'sale_report_id' => $commission['sale_report_id'],
                            'user_id' => $commission['user_id'],
                        ],
                        [
                            'commission_percent' => $commission['commission_percent']
                        ]
                    );
                }
            }
            DB::commit();
            return SaleReportMapper::fromEloquent($saleReportEloquent);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
