<?php

namespace Src\Company\Project\Application\Mappers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Src\Company\Project\Application\DTO\AdvancePaymentData;
use Src\Company\Project\Application\DTO\SupplierCostingData;
use Src\Company\Project\Domain\Model\Entities\AdvancePayment;
use Src\Company\Project\Domain\Model\Entities\SupplierCosting;
use Src\Company\Project\Infrastructure\EloquentModels\AdvancePaymentEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingEloquentModel;

class AdvancePaymentMapper
{
    public static function fromRequest(Request $request, ?int $id = null): AdvancePayment
    {
        return new AdvancePayment(
            id: $id,
            title: $request->title,
            amount: $request->amount,
            payment_date: $request->payment_date,
            remark: $request->filled('remark') ? $request->input('remark') : null,
            status: $request->filled('status') ? $request->input('status') : null,
            user_id: $request->user_id,
            sale_report_id: $request->sale_report_id,
        );
    }

    public static function fromEloquent(AdvancePaymentEloquentModel $advancePaymentEloquentModel): AdvancePaymentData
    {
        return new AdvancePaymentData(
            id: $advancePaymentEloquentModel->id,
            title: $advancePaymentEloquentModel->title,
            amount: $advancePaymentEloquentModel->amount,
            payment_date: $advancePaymentEloquentModel->payment_date,
            remark: $advancePaymentEloquentModel->remark,
            status: $advancePaymentEloquentModel->status,
            user_id: $advancePaymentEloquentModel->user_id,
            sale_report_id: $advancePaymentEloquentModel->sale_report_id,
        );
    }

    public static function toEloquent(AdvancePayment $advancePayment): AdvancePaymentEloquentModel
    {
        $advancePaymentEloquent = new AdvancePaymentEloquentModel();
        if ($advancePayment->id) {
            $advancePaymentEloquent = AdvancePaymentEloquentModel::query()->findOrFail($advancePayment->id);
        }
        $advancePaymentEloquent->title = $advancePayment->title;
        $advancePaymentEloquent->amount = $advancePayment->amount;
        $advancePaymentEloquent->payment_date = $advancePayment->payment_date;
        $advancePaymentEloquent->remark = $advancePayment->remark;
        $advancePaymentEloquent->status = $advancePayment->status;
        $advancePaymentEloquent->user_id = $advancePayment->user_id;
        $advancePaymentEloquent->sale_report_id = $advancePayment->sale_report_id;
        
        return $advancePaymentEloquent;
    }
}
