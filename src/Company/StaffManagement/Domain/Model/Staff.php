<?php

namespace Src\Company\StaffManagement\Domain\Model;

use Src\Common\Domain\AggregateRoot;

class Staff extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $user_id,
        public readonly ?int $rank_id,
        public readonly ?int $mgr_id,
        public readonly ?string $registry_no,
        public readonly ?string $rank_updated_at,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'rank_id' => $this->rank_id,
            'mgr_id' => $this->mgr_id,
            'registry_no' => $this->registry_no,
            'rank_updated_at' => $this->rank_updated_at,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
