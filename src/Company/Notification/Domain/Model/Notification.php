<?php

namespace Src\Company\Notification\Domain\Model;

use Src\Common\Domain\AggregateRoot;

class Notification extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        // TODO Add properties
    )
    {}

    public function toArray(): array
    {
        return [
            // TODO Add properties
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}