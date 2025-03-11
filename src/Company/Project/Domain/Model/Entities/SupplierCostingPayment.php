<?php

namespace Src\Company\Project\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class SupplierCostingPayment extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $bank_transaction_id,
        public readonly string $payment_date,
        public readonly int $payment_type,
        public readonly float $amount,
        public readonly ?string $remark,
        public readonly int $payment_method,
        public readonly int $status,
        public readonly int $payment_made_by,
        public readonly ?string $manager_signature,
        public readonly ?int $signed_by_manager_id
    )
    {}

    public function toArray(): array
    {
        return [
           'id' => $this->id,
           'bank_transaction_id' => $this->bank_transaction_id,
           'payment_date' => $this->payment_date,
           'payment_type' => $this->payment_type,
           'amount' => $this->amount,
           'remark' => $this->remark,
           'payment_method' => $this->payment_method,
           'status' => $this->status,
           'payment_made_by' => $this->payment_made_by,
           'manager_signature' => $this->manager_signature,
           'signed_by_manager_id' => $this->signed_by_manager_id,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}