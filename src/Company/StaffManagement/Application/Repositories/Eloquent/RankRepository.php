<?php

namespace Src\Company\StaffManagement\Application\Repositories\Eloquent;

use Src\Company\StaffManagement\Application\DTO\RankData;
use Src\Company\StaffManagement\Application\Mappers\RankMapper;
use Src\Company\StaffManagement\Domain\Model\Entities\Rank;
use Src\Company\StaffManagement\Domain\Repositories\RankRepositoryInterface;
use Src\Company\StaffManagement\Domain\Resources\RankResource;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\RankEloquentModel;

class RankRepository implements RankRepositoryInterface
{

    public function getRanks()
    {
        //rank list
        $rankEloquent = RankEloquentModel::query()->get();

        $ranks = RankResource::collection($rankEloquent);

        return $ranks;
    }

    public function store(Rank $rank): RankData
    {

        $rankEloquent = RankMapper::toEloquent($rank);

        $rankEloquent->save();

        return RankData::fromEloquent($rankEloquent);
    }

    public function update(Rank $rank): Rank
    {
        $rankArray = $rank->toArray();

        $rankEloquent = RankEloquentModel::query()->findOrFail($rank->id);

        $rankEloquent->fill($rankArray);

        $rankEloquent->save();

        return $rank;
    }

    public function delete(int $rankId): void
    {
        $roleEloquent = RankEloquentModel::query()->findOrFail($rankId);
        $roleEloquent->delete();
    }
}
