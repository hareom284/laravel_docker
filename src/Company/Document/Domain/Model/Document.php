<?php

namespace Src\Company\Document\Domain\Model;

use Src\Common\Domain\AggregateRoot;

class Document extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly ?string $document_file,
        public readonly string $file_type,
        public readonly bool $allow_customer_view,
        public readonly ?int $folder_id,
        public readonly int $project_id,
        public readonly ?string $date,
    )
    {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'document_file' => $this->document_file,
            'file_type' => $this->file_type,
            'allow_customer_view' => $this->allow_customer_view,
            'folder_id' => $this->folder_id,
            'project_id' => $this->project_id,
            'date' => $this->date,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}