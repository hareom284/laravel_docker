<?php

namespace Src\Company\CustomerManagement\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class RejectedReason extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $name,
        public readonly ?int $index,
        public readonly ?string $color_code,
    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'index' => $this->index,
            'color_code' => $this->color_code,
        ];
    }
}
