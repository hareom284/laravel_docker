<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class ThreeDDesign extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $name,
        public readonly ?string $date,
        public readonly ?string $last_edited,
        public readonly ?string $document_file,
        public readonly int $project_id,
        public readonly int $design_work_id,
        public readonly int $uploader_id
    )
    {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'date' => $this->date,
            'last_edited' => $this->last_edited,
            'document_file' => $this->document_file,
            'project_id' => $this->project_id,
            'design_work_id' => $this->design_work_id,
            'uploader_id' => $this->uploader_id,
            
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}