<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class EvoTemplateRoom extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $room_name
    )
    {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'room_name' => $this->room_name        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}