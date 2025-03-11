<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class ElectricalPlans extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $document_file,
        public readonly string $customer_signature,
        public readonly int $project_id
    )
    {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'document_file' => $this->document_file,
            'customer_signature' => $this->customer_signature,
            'project_id' => $this->project_id
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}