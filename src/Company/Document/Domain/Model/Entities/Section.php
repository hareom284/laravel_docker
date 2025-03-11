<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class Section extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $salesperson_id,
        public readonly ?int $index,
        public readonly ?string $name,
        public readonly ?string $calculation_type,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'salesperson_id' => $this->salesperson_id,
            'index' => $this->index,
            'name' => $this->name,
            'calculation_type' => $this->calculation_type,
        ];
    }
}
