<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class DesignWork extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $date,
        // public readonly ?string $document_date,
        public readonly string $name,
        public readonly string $document_file,
        public readonly ?string $scale,
        // public readonly ?string $request_status,
        // public readonly ?string $last_edited,
        // public readonly ?string $signed_date,
        public readonly ?int $designer_in_charge_id,
        public readonly int $project_id,
        // public readonly ?int $drafter_in_charge_id
    )
    {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            // 'document_date' => $this->document_date,
            'name' => $this->name,
            'document_file' => $this->document_file,
            'scale' => $this->scale,
            // 'request_status' => $this->request_status,
            // 'last_edited' => $this->last_edited,
            // 'signed_date' => $this->signed_date,
            'designer_in_charge_id' => $this->designer_in_charge_id,
            'project_id' => $this->project_id,
            // 'drafter_in_charge_id' => $this->drafter_in_charge_id
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}