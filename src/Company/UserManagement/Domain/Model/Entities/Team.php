<?php

namespace Src\Company\UserManagement\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class Team extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $team_name,
        public readonly ?int $team_leader_id,
        public readonly ?int $created_by
    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'team_name' => $this->team_name,
            'team_leader_id' => $this->team_leader_id,
            'created_by' => $this->created_by,
        ];
    }
}
