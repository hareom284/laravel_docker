<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Illuminate\Support\Facades\Date;
use Src\Common\Domain\AggregateRoot;

class Evo extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        // public readonly string $version_number,
        public readonly string $salesperson_signature,
        public readonly float $total_amount,
        public readonly float $grand_total,
        // public readonly string $signed_date,
        public readonly ?string $additional_notes,
        public readonly int $project_id,
        public readonly ?int $signed_by_salesperson_id,
    )
    {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'salesperson_signature' => $this->salesperson_signature,
            // 'version_number' => $this->version_number,
            'total_amount' => $this->total_amount,
            'grand_total' => $this->grand_total,
            // 'signed_date' => $this->signed_date,
            'additional_notes' => $this->additional_notes,
            'project_id' => $this->project_id,
            'signed_by_salesperson_id' => $this->signed_by_salesperson_id,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}