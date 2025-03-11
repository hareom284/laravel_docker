<?php

namespace Src\Company\StaffManagement\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class Rank extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $rank_name,
        public readonly ?string $tier,
        public readonly ?string $commission_percent,
        public readonly ?int $or_percent,
    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'rank_name' => $this->rank_name,
            'tier' => $this->tier,
            'commission_percent' => $this->commission_percent,
            'or_percent' => $this->or_percent,
        ];
    }
}
