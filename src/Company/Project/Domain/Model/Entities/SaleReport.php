<?php

namespace Src\Company\Project\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class SaleReport extends AggregateRoot implements \JsonSerializable
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

    public function toArray(): array
    {
        return [
           'id' => $this->id,
           'total_cost' => $this->total_cost,
           'total_sales' => $this->total_sales,
           'comm_issued' => $this->comm_issued,
           'or_issued' => $this->or_issued,
           'special_discount' => $this->special_discount,
           'gst' => $this->gst,
           'rebate' => $this->rebate,
           'net_profit_and_loss' => $this->net_profit_and_loss,
           'carpentry_job_amount' => $this->carpentry_job_amount,
           'carpentry_cost' => $this->carpentry_cost,
           'carpentry_comm' => $this->carpentry_comm,
           'carpentry_special_discount' => $this->carpentry_special_discount,
           'net_profit' => $this->net_profit
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}