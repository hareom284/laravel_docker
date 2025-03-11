<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class DeliveryOrder extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $project_id,
        public readonly ?string $do_no,
        public readonly ?string $po_no,
        public readonly ?string $quotation_no,
        public readonly ?string $date,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'do_no' => $this->do_no,
            'po_no' => $this->po_no,
            'quotation_no' => $this->quotation_no,
            'date' => $this->date
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
