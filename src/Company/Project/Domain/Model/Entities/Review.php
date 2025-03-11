<?php

namespace Src\Company\Project\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class Review extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly string $comments,
        public readonly int $stars,
        public readonly string $date,
        public readonly int $project_id,
        public readonly int $review_by,
        public readonly int $salesperson_id,
    )
    {}

    public function toArray(): array
    {
        return [
           'id' => $this->id,
           'title' => $this->title,
           'comments' => $this->comments,
           'stars' => $this->stars,
           'date' => $this->date,
           'project_id' => $this->project_id,
           'review_by' => $this->review_by,
           'salesperson_id' => $this->salesperson_id
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}