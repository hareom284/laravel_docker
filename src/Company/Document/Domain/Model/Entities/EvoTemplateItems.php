<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class EvoTemplateItems extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $description,
        public readonly float $unit_rate_without_gst,
        public readonly float $unit_rate_with_gst,
    )
    {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'unit_rate_without_gst' => $this->unit_rate_without_gst,
            'unit_rate_with_gst' => $this->unit_rate_with_gst,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}