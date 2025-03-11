<?php

namespace Src\Company\Project\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class Property extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $type_id,
        public readonly ?string $street_name,
        public readonly ?string $block_num,
        public readonly ?string $unit_num,
        public readonly ?string $postal_code
    )
    {}

    public function toArray(): array
    {
        return [
           'id' => $this->id,
           'type_id' => $this->type_id,
           'street_name' => $this->street_name,
           'block_num' => $this->block_num,
           'unit_num' => $this->unit_num,
           'postal_code' => $this->postal_code
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}