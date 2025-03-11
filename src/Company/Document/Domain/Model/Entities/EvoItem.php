<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Illuminate\Support\Facades\Date;
use Src\Common\Domain\AggregateRoot;

class EvoItem extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly? int $template_item_id,
        public readonly string $item_description,
        public readonly int $quantity,
        public readonly ?float $unit_rate,
        public readonly ?float $total,
        public readonly array $rooms
    )
    {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'template_item_id' => $this->template_item_id,
            'item_description' => $this->item_description,
            'quantity' => $this->quantity,
            'unit_rate' => $this->unit_rate,
            'total' => $this->total,
            'rooms' => $this->rooms
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}