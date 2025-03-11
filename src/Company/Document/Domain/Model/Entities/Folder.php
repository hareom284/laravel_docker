<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class Folder extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly bool $allow_customer_view,
        public readonly int $project_id

    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'allow_customer_view' => $this->allow_customer_view,
            'project_id' => $this->project_id
        ];
    }
}
