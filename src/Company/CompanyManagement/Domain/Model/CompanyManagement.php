<?php

namespace Src\Company\CompanyManagement\Domain\Model;

use Src\Common\Domain\AggregateRoot;

class CompanyManagement extends AggregateRoot implements \JsonSerializable
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