<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class VendorCategory extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $type
    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
        ];
    }
}
