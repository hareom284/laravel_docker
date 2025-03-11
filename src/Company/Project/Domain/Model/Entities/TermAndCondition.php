<?php

namespace Src\Company\Project\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class TermAndCondition extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title
        ];
    }
}
