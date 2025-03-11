<?php

namespace Src\Company\Project\Application\DTO;

use Illuminate\Http\Request;
use PhpParser\Node\Expr\Cast\Double;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;

class SaleReportData
{
    public function __construct(
        public readonly ?int $id,
        public readonly float $total_cost,
        public readonly float $total_sales,
        public readonly float $comm_issued,
        public readonly float $or_issued,
        public readonly float $special_discount,
        public readonly float $gst,
        public readonly float $rebate,
        public readonly float $net_profit_and_loss,
        public readonly float $carpentry_job_amount,
        public readonly float $carpentry_cost,
        public readonly float $carpentry_comm,
        public readonly float $carpentry_special_discount,
        public readonly float $net_profit
    )
    {}

    public static function fromRequest(Request $request, ?int $sale_report_id = null): SaleReportData
    {
        return new self(
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

    public static function fromEloquent(SaleReportEloquentModel $reportEloquent): self
    {
        return new self(
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

}