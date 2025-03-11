<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class PaymentTerm extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $payment_terms,
        public readonly ?int $project_id,
        public readonly ?string $is_default

    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'payment_terms' => $this->payment_terms,
            'project_id' => $this->project_id,
            'is_default' => $this->is_default
        ];
    }
}
