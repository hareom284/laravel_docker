<?php

namespace Src\Company\Project\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class PropertyType extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $type,
        public readonly int $is_predefined
    )
    {}

    public function toArray(): array
    {
        return [
           'id' => $this->id,
           'type' => $this->type,
           'is_predefined' => $this->is_predefined
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}