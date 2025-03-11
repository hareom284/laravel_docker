<?php

namespace Src\Company\Project\Application\Mappers;

use Illuminate\Http\Request;
use Src\Company\Project\Domain\Model\Entities\SaleReport;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;
use Src\Company\Project\Application\DTO\SaleReportData;

class SaleReportMapper {
    
    public static function fromRequest(Request $request, ?int $sale_report_id = null): SaleReport
    {

        return new SaleReport(
            id: $sale_report_id,
            total_cost: $request->float('total_cost'),
            total_sales: $request->float('total_sales'),
            comm_issued: $request->float('comm_issued'),
            or_issued: $request->float('or_issued'),
            special_discount: $request->float('special_discount'),
            gst: $request->float('gst'),
            rebate: $request->float('rebate'),
            net_profit_and_loss: $request->float('net_profit_and_loss'),
            carpentry_job_amount: $request->float('carpentry_job_amount'),
            carpentry_cost: $request->float('carpentry_cost'),
            carpentry_comm: $request->float('carpentry_comm'),
            carpentry_special_discount: $request->float('carpentry_special_discount'),
            net_profit: $request->float('net_profit')
        );
    }

    public static function fromEloquent(SaleReportEloquentModel $reportEloquent): SaleReportData
    {
        return new SaleReportData(
            id: $reportEloquent->id,
            total_cost: $reportEloquent->total_cost,
            total_sales: $reportEloquent->total_sales,
            comm_issued: $reportEloquent->comm_issued,
            or_issued: $reportEloquent->or_issued,
            special_discount: $reportEloquent->special_discount,
            gst: $reportEloquent->gst,
            rebate: $reportEloquent->rebate,
            net_profit_and_loss: $reportEloquent->net_profit_and_loss,
            carpentry_job_amount: $reportEloquent->carpentry_job_amount,
            carpentry_cost: $reportEloquent->carpentry_cost,
            carpentry_comm: $reportEloquent->carpentry_comm,
            carpentry_special_discount: $reportEloquent->carpentry_special_discount,
            net_profit: $reportEloquent->net_profit
        );
    }

    public static function toEloquent(SaleReport $saleReport): SaleReportEloquentModel
    {
        $reportEloquent = new SaleReportEloquentModel();
        if($saleReport->id)
        {
            $reportEloquent = SaleReportEloquentModel::query()->findOrFail($saleReport->id);
        }
        $reportEloquent->total_cost = $saleReport->total_cost;
        $reportEloquent->total_sales = $saleReport->total_sales;
        $reportEloquent->comm_issued = $saleReport->comm_issued;
        $reportEloquent->or_issued = $saleReport->or_issued;
        $reportEloquent->special_discount = $saleReport->special_discount;
        $reportEloquent->gst = $saleReport->gst;
        $reportEloquent->rebate = $saleReport->rebate;
        $reportEloquent->net_profit_and_loss = $saleReport->net_profit_and_loss;
        $reportEloquent->carpentry_job_amount = $saleReport->carpentry_job_amount;
        $reportEloquent->carpentry_cost = $saleReport->carpentry_cost;
        $reportEloquent->carpentry_comm = $saleReport->carpentry_comm;
        $reportEloquent->carpentry_special_discount = $saleReport->carpentry_special_discount;
        $reportEloquent->net_profit = $saleReport->net_profit;
        return $reportEloquent;
    }
}