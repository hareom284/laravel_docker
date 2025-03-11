<?php

namespace Src\Company\StaffManagement\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\StaffManagement\Domain\Model\Entities\Rank;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\RankEloquentModel;

class RankMapper
{
    public static function fromRequest(Request $request, ?int $rank_id = null): Rank
    {
        return new Rank(
            id: $rank_id,
            rank_name: $request->string('rank_name'),
            tier: $request->string('tier'),
            commission_percent: $request->string('commission_percent'),
            or_percent: $request->integer('or_percent'),
        );
    }

    public static function fromEloquent(RankEloquentModel $rankEloquent): Rank
    {
        return new Rank(
            id: $rankEloquent->id,
            rank_name: $rankEloquent->rank_name,
            tier: $rankEloquent->tier,
            commission_percent: $rankEloquent->commission_percent,
            or_percent: $rankEloquent->or_percent
        );
    }

    public static function toEloquent(Rank $rank): RankEloquentModel
    {
        $rankEloquent = new RankEloquentModel();
        if ($rank->id) {
            $rankEloquent = RankEloquentModel::query()->findOrFail($rank->id);
        }
        $rankEloquent->rank_name = $rank->rank_name;
        $rankEloquent->tier = $rank->tier;
        $rankEloquent->commission_percent = $rank->commission_percent;
        $rankEloquent->or_percent = $rank->or_percent;
        return $rankEloquent;
    }
}
