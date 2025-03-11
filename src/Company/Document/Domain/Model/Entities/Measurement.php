<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class Measurement extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly int $fixed,

    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'fixed' => $this->fixed
        ];
    }
}
