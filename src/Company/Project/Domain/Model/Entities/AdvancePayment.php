<?php

namespace Src\Company\Project\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class AdvancePayment extends AggregateRoot implements \JsonSerializable
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

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}