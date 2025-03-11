<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class HandoverCertificate extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $project_id,
        public readonly ?int $signed_by_manager_id,
        public readonly ?string $date,
        public readonly ?string $last_edited,
        public readonly ?string $customer_signature,
        public readonly string $salesperson_signature,
        public readonly ?int $signed_by_salesperson_id,
        public readonly ?string $manager_signature,
        public readonly int $status,

    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'signed_by_manager_id' => $this->signed_by_manager_id,
            'date' => $this->date,
            'last_edited' => $this->last_edited,
            'customer_signature' => $this->customer_signature,
            'salesperson_signature' => $this->salesperson_signature,
            'signed_by_salesperson_id' => $this->signed_by_salesperson_id,
            'manager_signature' => $this->manager_signature,
            'status' => $this->status,
        ];
    }
}
