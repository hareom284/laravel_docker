<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use stdClass;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\Project\Application\DTO\SaleReportData;
use Src\Company\Project\Domain\Model\Entities\SaleReport;
use Src\Company\Project\Application\Mappers\SaleReportMapper;
use Src\Company\Document\Infrastructure\EloquentModels\EvoEloquentModel;
use Src\Company\Project\Domain\Repositories\SaleReportRepositoryInterface;
use Src\Company\System\Infrastructure\EloquentModels\KpiRecordEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SaleCommissionEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;
use Src\Company\Project\Domain\Resources\SaleReportResource;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\SalepersonYearlyKpiEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\SalepersonMonthlyKpiEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class SaleReportRepository implements SaleReportRepositoryInterface
{
    public function getSaleReportByProjectId($projectId)
    {
        $saleReportEloquent = SaleReportEloquentModel::with('saleCommissions')->where('project_id', $projectId)->first();

        return $saleReportEloquent;
    }

    public function getSaleReportByYear($companyId, $year, $startDate, $endDate)
    {
        $renoDocsQuery = RenovationDocumentsEloquentModel::with('projects.properties', 'projects.salespersons', 'projects.supplierCostings')->whereNotNull('signed_date');

        if (!is_null($year)) {
            $renoDocsQuery->whereYear('signed_date', $year);
        }

        if (!is_null($startDate)) {
            $renoDocsQuery->when($startDate, function ($query) use ($startDate) {
                $query->where('signed_date', '>=', $startDate);
            });
        }

        if (!is_null($endDate)) {
            $renoDocsQuery->when($endDate, function ($query) use ($endDate) {
                $query->where('signed_date', '<=', $endDate);
            });
        }

        if (!is_null($companyId)) {
            $renoDocsQuery->whereHas('projects', function ($query) use ($companyId) {
                if ($companyId) {
                    $query->where('company_id', $companyId);
                }
            });
        }

        $renoDocs = $renoDocsQuery->get();

        $evosQuery = EvoEloquentModel::with('projects.properties', 'projects.salespersons', 'projects.supplierCostings')->whereNotNull('signed_date');

        if (!is_null($year)) {
            $evosQuery->whereYear('signed_date', $year);
        }

        if (!is_null($startDate)) {
            $evosQuery->when($startDate, function ($query) use ($startDate) {
                $query->where('signed_date', '>=', $startDate);
            });
        }

        if (!is_null($endDate)) {
            $evosQuery->when($endDate, function ($query) use ($endDate) {
                $query->where('signed_date', '<=', $endDate);
            });
        }

        if (!is_null($companyId)) {
            $evosQuery->whereHas('projects', function ($query) use ($companyId) {
                if ($companyId) {
                    $query->where('company_id', $companyId);
                }
            });
        }
        $evos = $evosQuery->get();

        $renoYearlyData = $renoDocs->groupBy(function ($item) {
            return Carbon::parse($item->signed_date)->format('M');
        })->map(function ($groupedItems) {

            $total = 0;
            $totalCompanyEarning = 0;
            $totalSalepersonsComm = 0;

            $data = [];

            foreach ($groupedItems as $item) {
                // TODO: may need update for agreement_no
                if ($item->type === "FOC") {
                    continue; // Skip "FOC" items
                }

                $siteAddress = $item->projects->properties->block_num . " " . $item->projects->properties->street_name . "#" . $item->projects->properties->unit_num;
                $designers = $item->projects->salespersons->implode('first_name', ' & ');
                $totalCostingAmt = round($item->projects->saleReport->total_sales, 2);

                $result = $this->getEarningAndComm($item, $totalCostingAmt, $siteAddress);

                $version = '';
                switch ($item->type) {
                    case 'QUOTATION':
                        $version = $item->projects->agreement_no;
                        break;

                    case 'VARIATIONORDER':
                        $version = $item->projects->agreement_no . '/VO';
                        break;

                    case 'FOC':
                        $version = $item->projects->agreement_no . '/FOC';
                        break;

                    case 'CANCELLATION':
                        $version = $item->projects->agreement_no . '/CN';
                        break;

                    default:
                        # code...
                        break;
                }

                if ($item->type != 'QUOTATION') {
                    $version_number = RenovationDocumentsEloquentModel::where('project_id', $item->projects->id)
                        ->where('type', $item->type)
                        ->get();

                    foreach ($version_number as $key => $value) {

                        if ($item->id == $value->id) {
                            $version .= (string)$key + 1;
                        }
                    }
                }

                $total += ($item->type === "CANCELLATION") ? (-1 * $totalCostingAmt) : $totalCostingAmt; // Subtract for "Cancellation"

                //Fix beacouse now we are showing each reno docs
                $totalSales = ($item->type === "CANCELLATION") ? (-1 * $item->total_amount) : $item->total_amount;
                $totalCompanyEarning += $result['companyEarningAmount'];
                $totalSalepersonsComm += $result['salePersonComm'];

                $data[] = [
                    'invoice_no' => $item->projects->invoice_no,
                    'document_name' => $item->agreement_no ?? $version,
                    'document_type' => $item->type,
                    'site_address' => $siteAddress,
                    'designer' => $designers,
                    'total_sales' => round($totalSales, 2),
                    'signed_date' => $item->signed_date,
                    'company_earning_amount' => $result['companyEarningAmount'],
                    'saleperson_commission_amount' => $result['salePersonComm'],
                ];
            }
            Log::info('total earning amount: ' . $totalCompanyEarning);
            Log::info('total commission amount: ' . $totalSalepersonsComm);
            return [
                'total' => $total,
                'totalCompanyEarning' => $totalCompanyEarning,
                'totalSalepersonsComm' => $totalSalepersonsComm,
                'data' => $data,
            ];
        });

        $evoYearlyData = $evos->groupBy(function ($item) {
            return Carbon::parse($item->signed_date)->format('M');
        })->map(function ($groupedItems) {

            $total = 0;
            $totalCompanyEarning = 0;
            $totalSalepersonsComm = 0;


            $data = $groupedItems->map(function ($item) use (&$total, &$totalCompanyEarning, &$totalSalepersonsComm) {

                $siteAddress = $item->projects->properties->block_num . " " . $item->projects->properties->street_name . "#" . $item->projects->properties->unit_num;

                $designers = $item->projects->salespersons->implode('first_name', ' & ');

                $totalCostingAmt = round($item->projects->saleReport->total_sales, 2);
                $result = $this->getEarningAndComm($item, $totalCostingAmt, $siteAddress);

                $total += $totalCostingAmt;
                $totalCompanyEarning += round($result['companyEarningAmount'], 2);
                $totalSalepersonsComm += round($result['salePersonComm'], 2);

                return [
                    'invoice_no' => $item->projects->invoice_no,
                    'document_name' => $item->projects->agreement_no . '/EVO' . $item->version_number,
                    'document_type' => 'EVO',
                    'site_address' => $siteAddress,
                    'designer' => $designers,
                    'total_sales' => round($item->grand_total, 2),
                    'signed_date' => $item->signed_date,
                    'company_earning_amount' => $result['companyEarningAmount'],
                    'saleperson_commission_amount' => $result['salePersonComm'],
                ];
            });


            return [
                'total' => $total,
                'totalCompanyEarning' => $totalCompanyEarning,
                'totalSalepersonsComm' => $totalSalepersonsComm,
                'data' => $data,
            ];
        });

        $companyKpiEloquent = KpiRecordEloquentModel::query()->where('company_id', $companyId)->where('period', $year)->first();

        $combinedData = $evoYearlyData->concat($renoYearlyData)->groupBy(function ($item) {
            return Carbon::parse($item['data'][0]['signed_date'])->format('M');
        })->map(function ($groupedItems) {

            $total = 0;
            $totalCompanyEarningAmt = 0.00;
            $totalSalepersonsCommAmt = 0.00;

            $combinedArray = [];

            $groupedItems->map(function ($item) use (&$total, &$totalCompanyEarningAmt, &$totalSalepersonsCommAmt, &$combinedArray) {
                Log::info('total earning ' . $item['totalSalepersonsComm']);
                Log::info('type earning integer ' . is_integer($item['totalSalepersonsComm']));
                Log::info('type earning float ' . is_float($item['totalSalepersonsComm']));
                Log::info('type earning string ' . is_string($item['totalSalepersonsComm']));

                $total += $item['total'];
                $totalCompanyEarningAmt += $item['totalCompanyEarning'];
                $totalSalepersonsCommAmt += $item['totalSalepersonsComm'];

                foreach ($item['data'] as $data) {

                    array_push($combinedArray, $data);
                }
            });

            return [
                'total' => $total,
                'totalCompanyEarning' => $totalCompanyEarningAmt,
                'totalSalepersonsComm' => $totalSalepersonsCommAmt,
                'data' => $combinedArray
            ];
        });

        $kpiData =  $companyKpiEloquent ? $companyKpiEloquent->target : 0;

        $combinedData->prepend($kpiData, 'kpi');

        return $combinedData;
    }

    private function getEarningAndComm($item, $totalCostingAmt, $siteAddress)
    {
        $totalSupDisAmount = $item->projects->supplierCostings->sum('discount_amt');
        $totalSupActualAmt = $item->projects->supplierCostings->sum('payment_amt');

        $saleReport = $item->projects->saleReport;
        $totalCarpentryAmount = $saleReport->carpentry_job_amount - ($saleReport->carpentry_cost + $saleReport->carpentry_comm + $saleReport->carpentry_special_discount);

        $companyEarning = GeneralSettingEloquentModel::where('setting', 'company_earning_percentage')->first()?->value ?? 0;
        // $totalSalePersonCommAmt = collect($this->getSalePersons($item))->sum('commission');
        // $totalSalePersonCommPercentage = ($totalSalePersonCommAmt - $companyEarning)/ 100;

        $gst = $item->projects->company->gst;
        $totalAmountBefGST = $gst == 0 ? 0 : $saleReport->total_sales * $gst / (100 + $gst);
        $totalJobPLAmt = $totalCostingAmt - ($totalAmountBefGST + $totalSupActualAmt + $saleReport->special_discount);
        // $totalSalePersonCommPercentage = 100 - $totalSalePersonCommPercentage;
        // $orAmtResult = ($totalJobPLAmt * ($totalSalePersonCommAmt / 100));
        $totalAfterDeduct = $totalJobPLAmt * ($companyEarning / 100);

        $companyEarningAmount = $totalSupDisAmount + $totalCarpentryAmount + $totalAfterDeduct;
        $salePersonComm = $totalJobPLAmt - $companyEarningAmount;

        return [
            'companyEarningAmount' => $companyEarningAmount,
            'salePersonComm' => $salePersonComm
        ];
    }

    private function getSalePersons($item)
    {
        $saleperson = [];
        $minCommission = null;
        $equalCommission = null;
        $referral_commission = GeneralSettingEloquentModel::where('setting', 'referral_commission')->first();
        $company_earning_percentage = GeneralSettingEloquentModel::where('setting', 'company_earning_percentage')->first();
        $totalCommissionBase = $referral_commission ? $referral_commission->value : 0;
        $salespersonCount = count($item->projects->salespersons);
        $totalRankPercent = 0;

        if ($salespersonCount > 1) {
            // Calculate total rank percentage for all salespersons if there are more than 1
            foreach ($item->projects->salespersons as $salesperson) {
                $totalRankPercent += $salesperson->staffs->rank->commission_percent;
            }
        }

        foreach ($item->projects->salespersons as $salesperson) {
            $obj = new stdClass();
            $obj->company_earning_percentage = $company_earning_percentage->value;
            $obj->name = $salesperson->first_name . ' ' . $salesperson->last_name;
            if ($item->projects->contactUser) {
                if ($salespersonCount == 1) {
                    $obj->commission = $totalCommissionBase;
                } else {
                    $commissionPercent = $salesperson->staffs->rank->commission_percent;

                    if ($totalRankPercent > 0) {
                        $obj->commission = round(($commissionPercent / $totalRankPercent) * $totalCommissionBase);
                    } else {
                        $obj->commission = round($totalCommissionBase / $salespersonCount);
                    }
                }
            } else {
                $totalCommissionBase = 100;
                if ($salespersonCount == 1) {
                    if ($item->projects?->saleReport?->or_issued && $item->projects?->saleReport?->or_issued > 0) {
                        $obj->commission = $totalCommissionBase - $item->projects->saleReport->or_issued;
                    } else {
                        $obj->commission = $totalCommissionBase;
                    }
                } else {
                    $commissionPercent = $salesperson->staffs->rank->commission_percent;

                    if ($totalRankPercent > 0) {
                        $obj->commission = round(($commissionPercent / $totalRankPercent) * $totalCommissionBase);
                    } else {
                        $obj->commission = round($totalCommissionBase / $salespersonCount);
                    }
                }

                if ($minCommission === null || $obj->commission < $minCommission) {
                    $minCommission = $obj->commission;
                    if ($item->projects?->saleReport?->or_issued && $item->projects?->saleReport?->or_issued > 0 && $totalRankPercent > 100) {
                        $obj->commission -= $item->projects->saleReport->or_issued;
                    }
                }
                // $obj->commission = round(intval($salesperson->staffs->rank->commission_percent));
            }

            array_push($saleperson, $obj);
        }

        return $saleperson;
    }

    public function getSaleReportByMonth($companyId, $year, $month, $startDate, $endDate)
    {

        $startDateOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDateOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $renoDocsQuery = RenovationDocumentsEloquentModel::with('projects.properties', 'projects.salespersons')->whereNotNull('signed_date');

        if (!is_null($companyId)) {
            if ($companyId !== 0) {
                $renoDocsQuery->whereHas('projects', function ($query) use ($companyId) {
                    if ($companyId) {
                        $query->where('company_id', $companyId);
                    }
                });
            }
        }

        if (!is_null($startDateOfMonth) && !is_null($endDateOfMonth)) {
            $renoDocsQuery->whereBetween('signed_date', [$startDateOfMonth, $endDateOfMonth]);
        }

        if (!is_null($startDate)) {
            $renoDocsQuery->when($startDate, function ($query) use ($startDate) {
                $query->where('signed_date', '>=', $startDate);
            });
        }

        if (!is_null($endDate)) {

            $renoDocsQuery->when($endDate, function ($query) use ($endDate) {
                $query->where('signed_date', '<=', $endDate);
            });
        }

        $renoDocs = $renoDocsQuery->get();

        $evosQuery = EvoEloquentModel::with('projects.properties', 'projects.salespersons')->whereNotNull('signed_date');

        if (!is_null($companyId)) {
            $evosQuery->whereHas('projects', function ($query) use ($companyId) {
                if ($companyId) {
                    $query->where('company_id', $companyId);
                }
            });
        }

        if (!is_null($startDateOfMonth) && !is_null($endDateOfMonth)) {
            $evosQuery->whereBetween('signed_date', [$startDate, $endDate]);
        }

        if (!is_null($startDate)) {
            $evosQuery->when($startDate, function ($query) use ($startDate) {
                $query->where('signed_date', '>=', $startDate);
            });
        }

        if (!is_null($endDate)) {

            $evosQuery->when($endDate, function ($query) use ($endDate) {
                $query->where('signed_date', '<=', $endDate);
            });
        }

        $evos = $evosQuery->get();

        $renoTotalSales = 0;
        $evoTotalSales = $evos->sum('grand_total');

        $renoMonthlyData = $renoDocs->filter(function ($item) {
            return $item->type !== "FOC"; // Filter out items with type "FOC"
        })->map(function ($item) use (&$renoTotalSales) {

            $siteAddress = $item->projects->properties->block_num . " " . $item->projects->properties->street_name . "#" . $item->projects->properties->unit_num;
            $designers = $item->projects->salespersons->implode('first_name', ' & ');
            $totalCostingAmt = round($item->total_amount, 2);

            $version = '';
            switch ($item->type) {
                case 'QUOTATION':
                    $version = $item->projects->agreement_no;
                    break;

                case 'VARIATIONORDER':
                    $version = $item->projects->agreement_no . '/VO';
                    break;

                case 'FOC':
                    $version = $item->projects->agreement_no . '/FOC';
                    break;

                case 'CANCELLATION':
                    $version = $item->projects->agreement_no . '/CN';
                    break;

                default:
                    # code...
                    break;
            }

            if ($item->type != 'QUOTATION') {
                $version_number = RenovationDocumentsEloquentModel::where('project_id', $item->projects->id)
                    ->where('type', $item->type)
                    ->get();

                foreach ($version_number as $key => $value) {

                    if ($item->id == $value->id) {
                        $version .= (string)$key + 1;
                    }
                }
            }

            $renoTotalSales += ($item->type === "CANCELLATION") ? (-1 * $totalCostingAmt) : $totalCostingAmt; // Subtract for "Cancellation"

            return [
                'invoice_no' => $item->projects->invoice_no,
                'document_name' => $item->agreement_no ?? $version,
                'document_type' => $item->type,
                'site_address' => $siteAddress,
                'sale_persons' => $designers,
                'total_sales' => ($item->type === "CANCELLATION") ? (-1 * $totalCostingAmt) : $totalCostingAmt,

            ];
        });

        $evoMonthlyData = $evos->map(function ($item) {

            $siteAddress = $item->projects->properties->block_num . " " . $item->projects->properties->street_name . "#" . $item->projects->properties->unit_num;

            $designers = $item->projects->salespersons->implode('first_name', ' & ');

            $totalCostingAmt = round($item->grand_total, 2);

            return  [
                'invoice_no' => $item->projects->invoice_no,
                'document_name' =>  $item->projects->agreement_no . '/EVO' . $item->version_number,
                'document_type' => 'EVO',
                'site_address' => $siteAddress,
                'sale_persons' => $designers,
                'total_sales' => $totalCostingAmt,
            ];
        });

        $totalSales = $renoTotalSales + $evoTotalSales;

        $monthlyData = $evoMonthlyData->concat($renoMonthlyData);

        $monthlyData->put('total', $totalSales);

        return $monthlyData;
    }

    public function getSalespersonReportByYear($salespersonId, $year)
    {
        $authUser = auth('sanctum')->user();
        $totalSales = 0;
        $monthlyData = collect();

        if ($authUser->roles->contains('name', 'Manager') && $salespersonId == 0) {
            // Manager: Get reports for all assigned salespersons
            foreach ($authUser->assignedSalepersons as $staff) {
                $staffId = $staff->id;
                $salePersonCommPercentage = $staff->rank->commission_percent;

                // Fetch sales data
                $renoDocs = RenovationDocumentsEloquentModel::with('projects.properties', 'projects.salespersons')
                    ->where('signed_by_salesperson_id', $staffId)
                    ->whereNotNull('signed_date')
                    ->whereYear('signed_date', $year)
                    ->get();

                $evos = EvoEloquentModel::with('projects.properties', 'projects.salespersons')
                    ->where('signed_by_salesperson_id', $staffId)
                    ->whereNotNull('signed_date')
                    ->whereYear('signed_date', $year)
                    ->get();

                // Process renovation and EVO documents
                $monthlyData = $monthlyData->concat($this->processYearlySalesData($renoDocs, $salePersonCommPercentage))
                    ->concat($this->processYearlySalesData($evos, $salePersonCommPercentage, true)); // Pass true for EVO
            }
        } else {
            // Salesperson: Get report for specific salesperson
            $staff = StaffEloquentModel::with('rank')->where('user_id', $salespersonId)->first();
            $salePersonCommPercentage = $staff?->rank?->commission_percent;

            // Fetch sales data
            $renoDocs = RenovationDocumentsEloquentModel::with('projects.properties', 'projects.salespersons')
                ->where('signed_by_salesperson_id', $staff->id)
                ->whereNotNull('signed_date')
                ->whereYear('signed_date', $year)
                ->get();

            $evos = EvoEloquentModel::with('projects.properties', 'projects.salespersons')
                ->where('signed_by_salesperson_id', $staff->id)
                ->whereNotNull('signed_date')
                ->whereYear('signed_date', $year)
                ->get();

            // Process renovation and EVO documents
            $monthlyData = $this->processYearlySalesData($renoDocs, $salePersonCommPercentage)
                ->concat($this->processYearlySalesData($evos, $salePersonCommPercentage, true)); // Pass true for EVO
        }

        // Group the data by month and sum up totals
        $combinedData = $monthlyData->groupBy(function ($item) {
            return Carbon::parse($item['data'][0]['signed_date'])->format('M'); // Group by month
        })->map(function ($groupedItems) {

            $total = 0;
            $combinedArray = [];

            $groupedItems->map(function ($item) use (&$total, &$combinedArray) {
                $total += $item['total'];

                foreach ($item['data'] as $data) {
                    array_push($combinedArray, $data);
                }
            });

            return [
                'total' => $total,
                'data' => $combinedArray
            ];
        });

        return $combinedData;
    }


    public function getSalespersonReportByMonth($salespersonId, $month, $year)
    {
        $authUser = auth('sanctum')->user();
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $totalSales = 0;
        $monthlyData = collect();

        if ($authUser->roles->contains('name', 'Manager') && $salespersonId == 0) {
            // Manager: Get reports for all assigned salespersons
            foreach ($authUser->assignedSalepersons as $staff) {
                $staffId = $staff->id;
                $salePersonCommPercentage = $staff->rank->commission_percent;

                // Fetch sales data
                $renoDocs = RenovationDocumentsEloquentModel::with('projects.properties', 'projects.salespersons')
                    ->where('signed_by_salesperson_id', $staffId)
                    ->whereNotNull('signed_date')
                    ->whereBetween('signed_date', [$startDate, $endDate])
                    ->get();

                $evos = EvoEloquentModel::with('projects.properties', 'projects.salespersons')
                    ->where('signed_by_salesperson_id', $staffId)
                    ->whereNotNull('signed_date')
                    ->whereBetween('signed_date', [$startDate, $endDate])
                    ->get();

                // Process renovation and EVO documents
                $monthlyData = $monthlyData->concat($this->processSalesData($renoDocs, $salePersonCommPercentage, $totalSales))
                    ->concat($this->processSalesData($evos, $salePersonCommPercentage, $totalSales, true));
            }
        } else {
            // Salesperson: Get report for specific salesperson
            $staff = StaffEloquentModel::with('rank')->where('user_id', $salespersonId)->first();
            $salePersonCommPercentage = $staff?->rank?->commission_percent;

            $renoDocs = RenovationDocumentsEloquentModel::with('projects.properties', 'projects.salespersons')
                ->where('signed_by_salesperson_id', $staff->id)
                ->whereNotNull('signed_date')
                ->whereBetween('signed_date', [$startDate, $endDate])
                ->get();

            $evos = EvoEloquentModel::with('projects.properties', 'projects.salespersons')
                ->where('signed_by_salesperson_id', $staff->id)
                ->whereNotNull('signed_date')
                ->whereBetween('signed_date', [$startDate, $endDate])
                ->get();

            // Process renovation and EVO documents
            $monthlyData = $this->processSalesData($renoDocs, $salePersonCommPercentage, $totalSales)
                ->concat($this->processSalesData($evos, $salePersonCommPercentage, $totalSales, true));
        }

        // Add total sales to the collection
        $monthlyData->put('total', round($totalSales, 2));

        return $monthlyData;
    }

    protected function getVersion($item)
    {
        $version = '';

        switch ($item->type) {
            case 'QUOTATION':
                $version = $item->projects->agreement_no;
                break;

            case 'VARIATIONORDER':
                $version = $item->projects->agreement_no . '/VO';
                break;

            case 'FOC':
                $version = $item->projects->agreement_no . '/FOC';
                break;

            case 'CANCELLATION':
                $version = $item->projects->agreement_no . '/CN';
                break;

            default:
                break;
        }

        if ($item->type != 'QUOTATION') {
            $version_number = RenovationDocumentsEloquentModel::where('project_id', $item->projects->id)
                ->where('type', $item->type)
                ->get();

            foreach ($version_number as $key => $value) {
                if ($item->id == $value->id) {
                    $version .= (string)($key + 1); // Add version number
                }
            }
        }

        return $version;
    }

    protected function processSalesData($documents, $salePersonCommPercentage, &$totalSales, $isEvo = false)
    {
        return $documents
            // ->filter(function ($item) use ($isEvo) {
            //     return !$isEvo ? $item->type !== "FOC" : true;
            // }) // temporary comment out this filter part to be able to see FOC doc in report, if not reopen the comment
            ->map(function ($item) use ($salePersonCommPercentage, &$totalSales, $isEvo) {

                $siteAddress = $item->projects->properties->block_num . " " . $item->projects->properties->street_name . "#" . $item->projects->properties->unit_num;
                $designers = $item->projects->salespersons->implode('first_name', ' & ');

                $totalCostingAmt = $isEvo ? round($item->grand_total, 2) : round($item->total_amount, 2);
                $salePersonComm = round($totalCostingAmt * ($salePersonCommPercentage / 100), 2);

                // Loop through renovation_sections to calculate the total vendor cost and margin_percent
                $totalVendorCost = 0;
                $profitAmount = 0;
                $quotedProfitPercent = 0;

                if (!empty($item->renovation_sections)) {
                    foreach ($item->renovation_sections as $renovation_section) {
                        // Fetch vendor cost for each section
                        $vendorIds = DB::table('section_vendor')
                            ->where('section_id', $renovation_section->sections->id)
                            ->pluck('vendor_id')
                            ->toArray();

                        $vendorCost = 0;
                        if (!empty($vendorIds)) {
                            $vendorCost = SupplierCostingEloquentModel::where('project_id', $item->project_id)
                                ->whereIn('vendor_id', $vendorIds)
                                ->sum(DB::raw('payment_amt - discount_amt'));
                        }

                        // Add this section's vendor cost to the total vendor cost
                        $totalVendorCost += $vendorCost ?? 0;

                        // Calculate profit amount for the section
                        $sectionProfitAmount = $renovation_section->total_price - $vendorCost;
                        $profitAmount += $sectionProfitAmount;

                        // Calculate quoted profit percentage
                        if ($renovation_section->total_price > 0) {
                            $quotedProfitPercent += round(($sectionProfitAmount / $renovation_section->total_price) * 100, 2);
                        }
                    }
                }

                // Calculate the overall margin percentage for the document
                $margin_percent = $totalCostingAmt > 0 ? round(($profitAmount / $totalCostingAmt) * 100, 2) : 0;

                // Adjust total sales, subtract in case of cancellation for renoDocs only
                $totalSales += (!$isEvo && $item->type === "CANCELLATION") ? (-1 * $totalCostingAmt) : $totalCostingAmt;

                $version = $isEvo ? $item->projects->agreement_no . '/EVO' . $item->version_number : $this->getVersion($item);

                return [
                    'invoice_no' => $item->projects->invoice_no,
                    'document_name' => $version,
                    'site_address' => $siteAddress,
                    'sale_persons' => $designers,
                    'total_sales' => ($item->type === "CANCELLATION" && !$isEvo) ? (-1 * $totalCostingAmt) : $totalCostingAmt,
                    'comm_value' => ($item->type === "CANCELLATION" && !$isEvo) ? (-1 * $salePersonComm) : $salePersonComm,
                    'signed_date' => $item->signed_date,
                    'margin_percent' => $margin_percent,  // Calculated margin_percent based on sections
                    'is_or_issued' => $item->projects?->saleReport?->or_issued > 0 ? true : false,
                ];
            });
    }

    protected function processYearlySalesData($documents, $salePersonCommPercentage, $isEvo = false)
    {
        return $documents->groupBy(function ($item) {
            return Carbon::parse($item->signed_date)->format('M');
        })->map(function ($groupedItems) use ($salePersonCommPercentage, $isEvo) {

            $total = 0;
            $data = $groupedItems->map(function ($item) use (&$total, $salePersonCommPercentage, $isEvo) {
                $siteAddress = $item->projects->properties->block_num . " " . $item->projects->properties->street_name . "#" . $item->projects->properties->unit_num;
                $designers = $item->projects->salespersons->implode('first_name', ' & ');

                $totalCostingAmt = $isEvo ? round($item->grand_total, 2) : round($item->total_amount, 2);
                $salePersonComm = round($totalCostingAmt * ($salePersonCommPercentage / 100), 2);

                $totalVendorCost = 0;
                $profitAmount = 0;
                $quotedProfitPercent = 0;
                $renovationTotalAmount = 0;
                if (!empty($item->renovation_sections)) {
                    foreach ($item->renovation_sections as $renovation_section) {
                        $vendorIds = DB::table('section_vendor')
                            ->where('section_id', $renovation_section->sections->id)
                            ->pluck('vendor_id')
                            ->toArray();

                        $vendorCost = 0;
                        if (!empty($vendorIds)) {
                            $vendorCost = SupplierCostingEloquentModel::where('project_id', $item->project_id)
                                ->whereIn('vendor_id', $vendorIds)
                                ->sum(DB::raw('payment_amt - discount_amt'));
                        }

                        $totalVendorCost += $vendorCost ?? 0;

                        $sectionProfitAmount = $renovation_section->total_price - $vendorCost;
                        $profitAmount += $sectionProfitAmount;
                        $renovationTotalAmount += $renovation_section->total_price;

                        // Calculate quoted profit percentage
                        if ($renovation_section->total_price > 0) {
                            $quotedProfitPercent += round(($sectionProfitAmount / $renovation_section->total_price) * 100, 2);
                        }
                    }
                }

                // Calculate the overall margin percentage for the document
                $margin_percent = $totalCostingAmt > 0 ? round(($profitAmount / $totalCostingAmt) * 100, 2) : 0;

                $total += ($item->type === "CANCELLATION" && !$isEvo) ? (-1 * $totalCostingAmt) : $totalCostingAmt;

                $version = $isEvo ? $item->projects->agreement_no . '/EVO' . $item->version_number : $this->getVersion($item);

                return [
                    'invoice_no' => $item->projects->invoice_no,
                    'document_name' => $version,
                    'site_address' => $siteAddress,
                    'sale_persons' => $designers,
                    'total_sales' => ($item->type === "CANCELLATION" && !$isEvo) ? (-1 * $totalCostingAmt) : $totalCostingAmt,
                    'comm_value' => ($item->type === "CANCELLATION" && !$isEvo) ? (-1 * $salePersonComm) : $salePersonComm,
                    'signed_date' => $item->signed_date,
                    'profit_amount' => $profitAmount,
                    'margin_percent' => $margin_percent,  // Calculated margin_percent based on sections
                    'is_or_issued' => $item->projects?->saleReport?->or_issued > 0 ? true : false,
                ];
            });

            return [
                'total' => $total,
                'data' => $data
            ];
        });
    }



    /* public function getSalespersonReportByYear($salespersonId,$year)
    {
        $saleReports = SaleReportEloquentModel::whereHas('project', function ($query) use ($salespersonId,$year) {
            $query->whereHas('salespersons', function ($subQuery) use ($salespersonId) {
                $subQuery->where('salesperson_id', $salespersonId);
            });
        })
        ->whereYear('created_at',$year)
        ->where('total_sales', '!=' , 0)
        ->where('total_sales', '!=' , NULL)
        ->with('project')
        ->get();

        $yearlyData = $saleReports->groupBy(function ($item) {
            return Carbon::parse($item->created_at)->format('M');
        })->map(function ($groupedItems) {
            $total = 0;
            $data = $groupedItems->map(function ($item) use (&$total) {

                $siteAddress = $item->project->properties->block_num . " " . $item->project->properties->street_name . "#" . $item->project->properties->unit_num;

                $designers = $item->project->salespersons->implode('first_name', ' & ');

                $totalCostingAmt = $item->total_cost + $item->gst;

                $totalCostingAmt = round($totalCostingAmt, 2);

                $deductAfterCostingAmt = $item->total_sales - $totalCostingAmt;

                $deductAfterCostingAmt = round($deductAfterCostingAmt, 2);

                $salePersonComm = ($deductAfterCostingAmt) * ($item->comm_issued / 100);

                $salePersonComm = round($salePersonComm, 2);

                $totalCostingWithComm = $totalCostingAmt + $salePersonComm;

                $firstBracketValue = round(($item->total_sales - $totalCostingWithComm), 2);

                $secondBracketValue = round(($item->total_sales - $item->gst), 2);

                $percentValue = round(($firstBracketValue / $secondBracketValue), 2);

                $percentage = $percentValue * 100;

                $total += $item->total_sales;

                return [
                    'invoice_no' => $item->project->invoice_no,
                    'site_address' => $siteAddress,
                    'designer' => $designers,
                    'total_sales' => $item->total_sales,
                    'comm_value' => $salePersonComm,
                    'percent_value' => $percentage,
                ];
            });

            return [
                'total' => $total,
                'data' => $data,
            ];
        });

        return $yearlyData;
    } */

    /* public function getSalespersonReportByMonth($salespersonId,$month,$year)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $saleReports = SaleReportEloquentModel::whereHas('project', function ($query) use ($salespersonId,$month) {
            $query->whereHas('salespersons', function ($subQuery) use ($salespersonId) {
                $subQuery->where('salesperson_id', $salespersonId);
            });
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->where('total_sales', '!=' , 0)
        ->where('total_sales', '!=' , NULL)
        ->with('project','project.salespersons')
        ->get();


        $totalSales = $saleReports->sum('total_sales');
        $monthlyData = $saleReports->map(function ($saleReport) use ($salespersonId) {

            $project = $saleReport->project;

            $siteAddress = $project->properties->block_num . " " . $project->properties->street_name . "#" . $project->properties->unit_num;

            $salePerson = $project->salespersons->where('id', $salespersonId)->first();

            return  [
                'invoice_no' => $project->invoice_no,
                'site_address' => $siteAddress,
                'sale_persons'=> $salePerson->first_name .' '.$salePerson->last_name,
                'total_sales' => $saleReport->total_sales,
            ];
        });

        $monthlyData->put('total', $totalSales);

        return $monthlyData;
    }*/


    public function getSalepersonKpiReportMonth($salespersonId, $month, $year)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $saleperson = StaffEloquentModel::query()->where('user_id', $salespersonId)->first();

        $saleperson_id = $saleperson->id;

        $salepersonKpiEloquent = SalepersonMonthlyKpiEloquentModel::query()->where('saleperson_id', $saleperson_id)->where('year', $year)->where('month', $month)->first();

        //$saleReports = SaleReportEloquentModel::whereHas('project', function ($query) use ($salespersonId,$month) {
        //    $query->whereHas('salespersons', function ($subQuery) use ($salespersonId) {
        //        $subQuery->where('salesperson_id', $salespersonId);
        //    });
        //})
        //->whereBetween('created_at', [$startDate, $endDate])
        //->where('total_sales', '!=' , 0)
        //->where('total_sales', '!=' , NULL)
        //->with('project','project.salespersons')
        //->get();

        $renoDocs = RenovationDocumentsEloquentModel::with('projects.properties', 'projects.salespersons')->where('signed_by_salesperson_id', $saleperson_id)
            ->whereNotNull('signed_date')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $evos = EvoEloquentModel::with('projects.properties', 'projects.salespersons')->where('signed_by_salesperson_id', $saleperson_id)
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

        return  [
            'current_sales' => $totalSales,
            'kpi' => isset($salepersonKpiEloquent) ? $salepersonKpiEloquent->target : 0
        ];
    }

    public function getSalepersonKpiReportYear($salespersonId, $year)
    {
        $saleReports = SaleReportEloquentModel::whereHas('project', function ($query) use ($salespersonId) {
            $query->whereHas('salespersons', function ($subQuery) use ($salespersonId) {
                $subQuery->where('salesperson_id', $salespersonId);
            });
        })
            ->whereYear('created_at', $year)
            ->where('total_sales', '!=', 0)
            ->where('total_sales', '!=', NULL)
            ->with('project', 'project.salespersons')
            ->get();

        $totalSales = $saleReports->sum('total_sales');

        $saleperson = StaffEloquentModel::query()->where('user_id', $salespersonId)->first();

        $saleperson_id = $saleperson->id;

        $salepersonKpiEloquent = SalepersonYearlyKpiEloquentModel::query()->where('saleperson_id', $saleperson_id)->where('year', $year)->first();
        //$salepersonKpiEloquent->management_target
        return  [
            'current_sales' => $totalSales,
            'kpi' => $salepersonKpiEloquent ? $salepersonKpiEloquent->management_target : 0
        ];
    }

    public function salepersonSaleReportWithKpiInYear($salepersonId, $year)
    {
        $saleperson = StaffEloquentModel::query()->where('user_id', $salepersonId)->first();

        $saleperson_id = $saleperson->id;

        $salepersonKpiEloquent = SalepersonMonthlyKpiEloquentModel::query()->where('saleperson_id', $saleperson_id)->where('year', $year)->get();

        $monthlySalepersonKpi = $salepersonKpiEloquent->map(function ($item) {
            return [
                'month' => $item->month,
                'target' => $item->target
            ];
        });

        $renoDocs = RenovationDocumentsEloquentModel::with('projects.properties', 'projects.salespersons')->where('signed_by_salesperson_id', $saleperson_id)
            ->whereNotNull('signed_date')
            ->whereYear('signed_date', $year)
            ->get();

        $evos = EvoEloquentModel::with('projects.properties', 'projects.salespersons')->where('signed_by_salesperson_id', $saleperson_id)
            ->whereNotNull('signed_date')
            ->whereYear('signed_date', $year)
            ->get();


        $renoYearlyData = $renoDocs->groupBy(function ($item) {
            return Carbon::parse($item->signed_date)->format('M');
        })->map(function ($groupedItems) {

            $total = 0;

            $data = [];

            foreach ($groupedItems as $item) {
                if ($item->type === "FOC") {
                    continue; // Skip "FOC" items
                }

                $totalCostingAmt = round($item->total_amount, 2);

                $total += ($item->type === "CANCELLATION") ? (-1 * $totalCostingAmt) : $totalCostingAmt; // Subtract for "Cancellation"

                $data[] = ['signed_date' => $item->signed_date];
            }

            return ['total' => $total, 'data' => $data];
        });

        $evoYearlyData = $evos->groupBy(function ($item) {
            return Carbon::parse($item->signed_date)->format('M');
        })->map(function ($groupedItems) {

            $total = 0;

            $data = $groupedItems->map(function ($item) use (&$total) {

                $totalCostingAmt = round($item->grand_total, 2);

                $total += $totalCostingAmt;

                return ['signed_date' => $item->signed_date];
            });


            return ['total' => $total, 'data' => $data];
        });

        $saleYearlyData =  $evoYearlyData->concat($renoYearlyData)->groupBy(function ($item) {
            return Carbon::parse($item['data'][0]['signed_date'])->format('M');
        })->map(function ($groupedItems) {

            $total = 0;

            $groupedItems->map(function ($item) use (&$total) {
                $total += $item['total'];
            });

            return ['total' => $total];
        });

        $finalResult = [
            'sales' => $saleYearlyData,
            'kpi' => $monthlySalepersonKpi
        ];

        return $finalResult;
    }

    public function companySaleReportWithKpiInYear($companyId, $year)
    {
        $saleReports = SaleReportEloquentModel::whereHas('project', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->whereBetween('created_at', [$year . '-01-01', $year . '-12-31'])
            ->where('total_sales', '!=', 0)
            ->where('total_sales', '!=', NULL)
            ->with('project')
            ->get();

        $yearlyData = $saleReports->groupBy(function ($item) {
            return Carbon::parse($item->created_at)->format('M');
        })->map(function ($groupedItems) {
            $total = 0;
            $groupedItems->map(function ($item) use (&$total) {

                $total += $item->total_sales;
            });

            return [
                'total' => $total,
            ];
        });

        $companyKpiEloquent = KpiRecordEloquentModel::query()->where('company_id', $companyId)->where('period', $year)->first();

        $finalResult = [
            'sales' => $yearlyData,
            'kpi' => $companyKpiEloquent ? $companyKpiEloquent->target : 0
        ];

        return $finalResult;
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

    public function destroy(int $sale_report_id): void
    {

        $saleReportEloquent = SaleReportEloquentModel::query()->findOrFail($sale_report_id);

        $saleReportEloquent->delete();
    }

    public function getPendingApprovalDocuments($filters = [])
    {
        $perPage = $filters['perPage'] ?? 10;
        $saleReports = SaleReportEloquentModel::where('file_status', 'MARKED')->with(['project.property', 'saleCommissions'])->orderBy('updated_at', 'desc')->paginate($perPage);
        $saleReportData = SaleReportResource::collection($saleReports);

        $links = [
            'first' => $saleReportData->url(1),
            'last' => $saleReportData->url($saleReportData->lastPage()),
            'prev' => $saleReportData->previousPageUrl(),
            'next' => $saleReportData->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $saleReportData->currentPage(),
            'from' => $saleReportData->firstItem(),
            'last_page' => $saleReportData->lastPage(),
            'path' => $saleReportData->url($saleReportData->currentPage()),
            'per_page' => $perPage,
            'to' => $saleReportData->lastItem(),
            'total' => $saleReportData->total(),
        ];
        $responseData['data'] = $saleReportData;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function getManagerPendingApprovalDocuments($filters = [])
    {
        $perPage = $filters['perPage'] ?? 10;
        $saleReports = SaleReportEloquentModel::where('file_status', 'UPLOADED')->with(['project.property', 'saleCommissions'])->orderBy('updated_at', 'desc');
        $authUser = auth('sanctum')->user();
        if ($authUser->roles->contains('name', 'Manager')) {
            $staff = $authUser->staffs;
            $saleReports->whereHas('project', function ($query) use ($staff) {
                $query->whereHas('salespersons', function ($query) use ($staff) {
                    $query->whereIn('users.id', function ($subQuery) use ($staff) {
                        $subQuery->select('user_id')
                            ->from('staffs')
                            ->where('mgr_id', $staff->user_id);
                    });
                });
            });
        }
        $saleReportResult = $saleReports->paginate($perPage);
        $saleReportData = SaleReportResource::collection($saleReportResult);

        $links = [
            'first' => $saleReportData->url(1),
            'last' => $saleReportData->url($saleReportData->lastPage()),
            'prev' => $saleReportData->previousPageUrl(),
            'next' => $saleReportData->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $saleReportData->currentPage(),
            'from' => $saleReportData->firstItem(),
            'last_page' => $saleReportData->lastPage(),
            'path' => $saleReportData->url($saleReportData->currentPage()),
            'per_page' => $perPage,
            'to' => $saleReportData->lastItem(),
            'total' => $saleReportData->total(),
        ];
        $responseData['data'] = $saleReportData;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function markedSaleReport(int $saleReportId, $request): void
    {
        $saleReport = SaleReportEloquentModel::find($saleReportId);
        if ($saleReport) {
            $saleReport->update(['file_status' => $request->file_status]);
            if ($request->file_status == 'APPROVED') {
                $project = ProjectEloquentModel::find($saleReport->project_id);
                $project->update([
                    'project_status' => 'Completed'
                ]);

                $customerUsers = $project->customersPivot;
                if ($customerUsers && !empty($customerUsers)) {
                    foreach ($customerUsers as $customerUser) {
                        $customerEloquent = CustomerEloquentModel::find($customerUser?->customers?->id);
                        if ($customerEloquent) {
                            $customerEloquent->update([
                                'status' => 3
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function signSaleReport($id, $data)
    {
        $saleReport = SaleReportEloquentModel::find($id);
        if ($saleReport) {
            if (isset($data['isManager'])) {
                if (!isset($data['manager_signature']) || !$data['manager_signature'] instanceof \Illuminate\Http\UploadedFile) {
                    return response()->json(['error' => 'Invalid signature file'], 422);
                }
                $saleReport->clearMediaCollection('manager_signatures');
                $saleReport->addMedia($data['manager_signature'])
                    ->toMediaCollection('manager_signatures');
                $saleReport->update(['file_status' => 'PENDING']);
            } else {
                if (!isset($data['salesperson_signature']) || !$data['salesperson_signature'] instanceof \Illuminate\Http\UploadedFile) {
                    return response()->json(['error' => 'Invalid signature file'], 422);
                }
                $saleReport->clearMediaCollection('salesperson_signatures');
                $saleReport->addMedia($data['salesperson_signature'])
                    ->toMediaCollection('salesperson_signatures');
                $saleReport->update(['file_status' => 'PENDING']);
            }
        }
        return $saleReport;
    }
}
