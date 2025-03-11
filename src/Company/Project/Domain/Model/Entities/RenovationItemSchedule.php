<?php

namespace Src\Company\Project\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class RenovationItemSchedule extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $project_id,
        public readonly array $renovation_item_id,
        public readonly array $start_date,
        public readonly array $end_date,
        public readonly array $show_in_timeline
    )
    {}

    public function toArray(): array
    {
        return [
           'id' => $this->id,
           'project_id' => $this->project_id,
           'renovation_item_id' => $this->renovation_item_id,
           'start_date' => $this->start_date,
           'end_date' => $this->end_date,
           'show_in_timeline' => $this->show_in_timeline
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}