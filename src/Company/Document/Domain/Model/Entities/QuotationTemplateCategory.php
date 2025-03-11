<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class QuotationTemplateCategory extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?int $salesperson_id,

    )
    {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'salesperson_id' => $this->salesperson_id,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}