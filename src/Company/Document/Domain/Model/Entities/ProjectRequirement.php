<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class ProjectRequirement extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly mixed $document_file,
        public readonly int $project_id

    ) {
    }



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'document_file' => $this->document_file,
            'project_id' => $this->project_id
        ];
    }
}
