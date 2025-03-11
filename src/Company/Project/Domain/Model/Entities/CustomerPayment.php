<?php

namespace Src\Company\Project\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class CustomerPayment extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $payment_type,
        public readonly ?string $invoice_no,
        public readonly ?string $invoice_date,
        public readonly ?string $description,
        public readonly int $index,
        public readonly float $amount,
        public readonly ?string $remark,
        public readonly ?int $bank_info,
        public readonly int $status,
        public readonly int $sale_report_id
    )
    {}

    public function toArray(): array
    {
        return [
           'id' => $this->id,
           'payment_type' => $this->payment_type,
           'payment_type' => $this->invoice_no,
           'description' => $this->description,
           'index' => $this->index,
           'amount' => $this->amount,
           'remark' => $this->remark,
           'bank_info' => $this->bank_info,
           'sale_report_id' => $this->sale_report_id,
           'status' => $this->status
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}