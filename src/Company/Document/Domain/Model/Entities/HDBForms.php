<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class HDBForms extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $project_id,
        public readonly string $name,
        public readonly string $document_file,
    )
    {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'name' => $this->name,
            'document_file' => $this->document_file
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}