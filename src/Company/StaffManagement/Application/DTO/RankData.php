<?php

namespace Src\Company\StaffManagement\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\RankEloquentModel;

class RankData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $rank_name,
        public readonly ?string $tier,
        public readonly ?string $commission_percent,
        public readonly ?int $or_percent,
    )
    {}

    public static function fromRequest(Request $request, ?int $rank_id = null): RankData
    {
        return new self(
            id: $rank_id,
            rank_name: $request->string('rank_name'),
            tier: $request->string('tier'),
            commission_percent: $request->string('commission_percent'),
            or_percent: $request->integer('or_percent')
        );
    }

    public static function fromEloquent(RankEloquentModel $rankEloquent): self
    {
        return new self(
            id: $rankEloquent->id,
            rank_name: $rankEloquent->rank_name,
            tier: $rankEloquent->tier,
            commission_percent: $rankEloquent->commission_percent,
            or_percent: $rankEloquent->or_percent,
        );
    }

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
