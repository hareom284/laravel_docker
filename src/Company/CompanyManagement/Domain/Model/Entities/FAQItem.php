<?php

namespace Src\Company\CompanyManagement\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class FAQItem extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $question,
        public readonly string $answer,
        public readonly ?int $project_id,
        public readonly ?int $customer_id,
        public readonly int $status
    )
    {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'answer' => $this->answer,
            'project_id' => $this->project_id,
            'customer_id' => $this->customer_id,
            'status' => $this->status
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}