<?php

namespace Src\Company\Project\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class PaymentType extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}
