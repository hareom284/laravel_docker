<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class AreaOfWork extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $name,
        public readonly ?int $index,
        public readonly ?int $section_id,
    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'index' => $this->index,
            'section_id' => $this->section_id,
        ];
    }
}
