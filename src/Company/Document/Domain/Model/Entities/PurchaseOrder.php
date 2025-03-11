<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class PurchaseOrder extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $project_id,
        public readonly int $vendor_id,
        public readonly string $date,
        public readonly string $attn,
        public readonly ?string $time,
        public readonly ?string $pages,
        public readonly ?string $sales_rep_signature,
        public readonly ?string $remark,
        public readonly ?string $delivery_date,
        public readonly ?string $delivery_time_of_the_day,
        public readonly ?int $purchase_order_number,

    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'vendor_id' => $this->vendor_id,
            'date' => $this->date,
            'attn' => $this->attn,
            'time' => $this->time,
            'pages' => $this->pages,
            'sales_rep_signature' => $this->sales_rep_signature,
            'remark' => $this->remark,
            'delivery_date' => $this->delivery_date,
            'delivery_time_of_the_day' => $this->delivery_time_of_the_day,
            'purchase_order_number' => $this->purchase_order_number,
        ];
    }
}
