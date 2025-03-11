<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class ProjectPortfolio extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $project_id,
        public readonly string $title,
        public readonly string $description,
    )
    {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'title' => $this->title,
            'description' => $this->description,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
