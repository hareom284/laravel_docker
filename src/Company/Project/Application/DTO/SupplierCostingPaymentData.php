<?php

namespace Src\Company\Project\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingPaymentEloquentModel;

class SupplierCostingPaymentData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $bank_transaction_id,
        public readonly string $payment_date,
        public readonly int $payment_type,
        public readonly ?float $amount,
        public readonly ?string $remark,
        public readonly int $payment_method,
        public readonly int $status,
        public readonly int $payment_made_by,
        public readonly ?string $manager_signature,
        public readonly ?int $signed_by_manager_id,
    )
    {}

    public static function fromRequest(Request $request, ?int $id = null): SupplierCostingPaymentData
    {
        return new self(
            id: $id,
            bank_transaction_id: $request->string('bank_transaction_id'),
            payment_date: $request->float('payment_date'),
            payment_type: $request->integer('payment_type'),
            amount: $request->float('amount'),
            remark: $request->string('remark') ?? null,
            payment_method: $request->integer('payment_method'),
            status: $request->integer('status'),
            payment_made_by: $request->integer('payment_made_by'),
            manager_signature: $request->string('manager_signature') ?? null,
            signed_by_manager_id: $request->string('signed_by_manager_id') ?? null
        );
    }

    public static function fromEloquent(SupplierCostingPaymentEloquentModel $supplierCostingEloquentModel): self
    {
        return new self(
            id: $supplierCostingEloquentModel->id,
            bank_transaction_id: $supplierCostingEloquentModel->bank_transaction_id,
            payment_date: $supplierCostingEloquentModel->payment_date,
            payment_type: $supplierCostingEloquentModel->payment_type,
            amount: $supplierCostingEloquentModel->amount,
            remark: $supplierCostingEloquentModel->remark,
            payment_method: $supplierCostingEloquentModel->payment_method,
            status: $supplierCostingEloquentModel->status,
            payment_made_by: $supplierCostingEloquentModel->payment_made_by,
            manager_signature: $supplierCostingEloquentModel->manager_signature,
            signed_by_manager_id: $supplierCostingEloquentModel->signed_by_manager_id,
        );
    }

    public function toArray(): array
    {
        return [
           'id' => $this->id,
           'bank_transaction_id' => $this->bank_transaction_id,
           'payment_date' => $this->payment_date,
           'payment_type' => $this->payment_type,
           'amount' => $this->amount,
           'remark' => $this->remark,
           'status' => $this->status,
           'payment_made_by' => $this->payment_made_by,
           'manager_signature' => $this->manager_signature,
           'manager_signature' => $this->manager_signature,
           'signed_by_manager_id' => $this->signed_by_manager_id,
        ];
    }
}