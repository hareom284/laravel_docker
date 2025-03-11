<?php

namespace Src\Company\Project\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class VendorInvoiceExpenseType extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly int $project_related
    )
    {}

    public function toArray(): array
    {
        return [
           'id' => $this->id,
           'name' => $this->name,
           'project_related' => $this->project_related
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}