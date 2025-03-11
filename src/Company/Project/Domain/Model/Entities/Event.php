<?php

namespace Src\Company\Project\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class Event extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $start_date,
        public readonly ?string $end_date,
        public readonly ?string $status,
        public readonly int $staff_id,
        public readonly ?int $project_id
    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->title,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'staff_id' => $this->staff_id,
            'project_id' => $this->project_id
        ];
    }
}
