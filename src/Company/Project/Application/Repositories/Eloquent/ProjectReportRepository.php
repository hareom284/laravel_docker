<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use stdClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\Company\Project\Domain\Resources\CustomerPaymentResource;
use Src\Company\Project\Domain\Resources\SupplierCostingResource;
use Src\Company\Project\Domain\Repositories\ProjectReportRepositoryInterface;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;

class ProjectReportRepository implements ProjectReportRepositoryInterface
{
    public function getProjectReport(int $projectId)
    {
        // Fetch the sale report with related data
        $saleReport = SaleReportEloquentModel::query()
            ->with(['customer_payments', 'project.company', 'saleCommissions', 'project.supplierCostings', 'project.salespersons.staffs.rank'])
            ->where('project_id', $projectId)
            ->first();

        // Fetch supplier costings
        $supplierCostings = SupplierCostingEloquentModel::where('project_id', $projectId)->get();

        // Calculate total cost
        $showCostWithoutRebate = GeneralSettingEloquentModel::query()
            ->where('setting', 'show_project_cost_without_rebate_to_designer')
            ->where('value', 'true')
            ->exists();

        $totalCost = $supplierCostings->sum(function ($costing) use ($showCostWithoutRebate) {
            return $showCostWithoutRebate
                ? $costing->payment_amt
                : ($costing->payment_amt - $costing->discount_amt);
        });

        // Initialize payment and sales values
        $payment = $saleReport->paid ?? 0;
        $totalSales = $saleReport->total_sales ?? 0;
        $netProfit = $totalSales - $totalCost;
        $profitMargin = $totalSales > 0 ? (($netProfit / $totalSales) * 100) : 0;
        $paymentPercentage = $totalSales > 0 ? (($payment / $totalSales) * 100) : 0;

        // Calculate GST
        $gstRate = $saleReport->project?->company?->gst ?? 0;
        $gst = $gstRate > 0 ? ($totalSales * $gstRate / (100 + $gstRate)) : 0;

        // Calculate salesperson commission
        $salePersonCommPercent = $saleReport->saleCommissions->sum('commission_percent') ?? 0;
        $totalSupplierCostAmt = $saleReport->project->supplierCostings->sum('payment_amt');

        // $salePersonCommBase = $totalSales - ($saleReport->special_discount + $totalSupplierCostAmt + $gst);

        if ($saleReport->saleCommissions && $salePersonCommPercent === 0) {
            $salePersonCommPercent = $saleReport->project->salespersons->sum(function ($salesperson) {
                return $salesperson->staffs?->rank->commission_percent ?? 0;
            });
            $salePersonCommPercent = min($salePersonCommPercent, 100);

            if ($saleReport->or_issued) {
                $salePersonCommPercent -= $saleReport->or_issued;
            } else {
                // $orIssued = GeneralSettingEloquentModel::query()
                //     ->where('setting', 'over_ride_percentage')
                //     ->value('value');
                $orIssued = $saleReport->project->salespersons[0]?->staffs?->rank?->or_percent ?? 0;
                $salePersonCommPercent -= $orIssued;
            }
        }
        $salePersonCommBase = ($netProfit * $salePersonCommPercent) / 100;
        // Prepare the response data
        $data = [
            "id" => $saleReport->id,
            'payment' => number_format($payment, 2, '.', ','),
            'cost' => number_format($totalCost, 2, '.', ','),
            'sales' => number_format($totalSales, 2, '.', ','),
            'profit' => number_format($netProfit, 2, '.', ','),
            'profit_margin' => round($profitMargin, 2),
            'payment_percentage' => round($paymentPercentage, 2),
            'supplier_cost' => SupplierCostingResource::collection($supplierCostings),
            'payments' => CustomerPaymentResource::collection($saleReport->customer_payments ?? []),
            'profit_and_loss' => $this->getProfileAndList($projectId),
            'saleperson_comm' => $salePersonCommBase,
            'saleperson_comm_percent' => $salePersonCommPercent,
            'document_file' => $saleReport->document_file,
            'file_status' => $saleReport->file_status,
            'project_status' => $saleReport?->project?->project_status ?? '',
            'salesperson_signature' => $saleReport?->getFirstMediaUrl('salesperson_signatures'),
            'manager_signature' => $saleReport?->getFirstMediaUrl('manager_signatures')

        ];

        return $data;
    }


    private function getProfileAndList($projectId) {
        $quotation = RenovationDocumentsEloquentModel::where('project_id', $projectId)
                    ->where('type','QUOTATION')
                    ->whereNotNull('signed_date')
                    ->first();

        $data = [];

        if(isset($quotation)) {
            foreach($quotation->renovation_sections as $renovation_section) {
                $vendors = DB::table('section_vendor')
                ->where('section_id', $renovation_section->sections->id)
                ->pluck('vendor_id')
                ->toArray();

                $vendor_cost = 0;

                if(!empty($vendors)) {
                    $vendor_cost = SupplierCostingEloquentModel::where('project_id', $projectId)
                                    ->whereIn('vendor_id', $vendors)
                                    ->selectRaw('SUM(payment_amt - discount_amt) as total_amount')
                                    ->value('total_amount');
                }
                
                $quoted_profit_amount = $quoted_profit_percent = 0;
                if($renovation_section->total_price > 0) {
                    $quoted_profit_amount = $renovation_section->total_price - $renovation_section->total_cost_price;

                    if($renovation_section->total_cost_price > 0)
                        $quoted_profit_percent = round(($quoted_profit_amount / $renovation_section->total_price) * 100, 2);
                }

                $data[] = [
                    "section_name" => $renovation_section->name,
                    "signed_amount" => round($renovation_section->total_price, 2),
                    "signed_cost_amount" => round($renovation_section->total_cost_price, 2),
                    "vendor_cost" => round($vendor_cost, 0),
                    "profit_amount" => round($renovation_section->total_price - $vendor_cost, 2),
                    "profit_percent" => $renovation_section->total_price > 0 ? round(($renovation_section->total_price - $vendor_cost) / $renovation_section->total_price * 100, 2) : 0,
                    "quoted_cost" => round($renovation_section->total_cost_price, 2),
                    "quoted_profit_amount" => round($quoted_profit_amount, 2),
                    "quoted_profit_percent" => $quoted_profit_percent
                ];
            }
            
        }

        return $data;
    }
}