<?php

namespace Src\Company\CustomerManagement\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class CheckList extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $description,
        public readonly ?int $is_completed,
        public readonly ?int $customer_id,
    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'is_completed' => $this->is_completed,
            'customer_id' => $this->customer_id,
        ];
    }
}
