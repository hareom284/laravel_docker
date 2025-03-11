<?php

namespace Src\Company\Project\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Project\Infrastructure\EloquentModels\AdvancePaymentEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingEloquentModel;

class AdvancePaymentData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly float $amount,
        public readonly string $payment_date,
        public readonly ?string $remark,
        public readonly ?int $status,
        public readonly int $user_id,
        public readonly int $sale_report_id,  
    )
    {}

    public static function fromRequest(Request $request, ?int $id = null): AdvancePaymentData
    {
        return new self(
            id: $id,
            title: $request->string('title'),
            amount: $request->float('amount'),
            payment_date: $request->string('payment_date'),
            remark: $request->string('remark') ?? null,
            status: $request->integer('status') ?? null,
            user_id: $request->integer('user_id'),
            sale_report_id: $request->integer('sale_report_id'),
        );
    }

    public static function fromEloquent(AdvancePaymentEloquentModel $advancePaymentEloquentModel): self
    {
        return new self(
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

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date,
            'remark' => $this->remark,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'sale_report_id' => $this->sale_report_id,
        ];
    }
}