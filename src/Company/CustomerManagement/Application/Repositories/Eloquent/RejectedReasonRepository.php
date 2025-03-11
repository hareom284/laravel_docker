<?php

namespace Src\Company\CustomerManagement\Application\Repositories\Eloquent;

use Src\Company\CustomerManagement\Application\DTO\RejectedReasonData;
use Src\Company\CustomerManagement\Application\Mappers\RejectedReasonMapper;
use Src\Company\CustomerManagement\Domain\Model\Entities\RejectedReason;
use Src\Company\CustomerManagement\Domain\Repositories\RejectedReasonRepositoryInterface;
use Src\Company\CustomerManagement\Domain\Resources\RejectedReasonResources;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\RejectedReasonsEloquentModel;

class RejectedReasonRepository implements RejectedReasonRepositoryInterface
{

    public function findAllRejectedReason()
    {
        $rejectedReasonEloquent = RejectedReasonsEloquentModel::query()->orderBy('index','asc')->get();

        $rejectedReasons = RejectedReasonResources::collection($rejectedReasonEloquent);

        return $rejectedReasons;

    }

    public function findRejectedReason($id){
        $rejectedReason = RejectedReasonsEloquentModel::find($id);
        return $rejectedReason;
    }

    public function store(RejectedReason $rejectedReason): RejectedReasonData
    {
        $rejectedReasonEloquent = RejectedReasonMapper::toEloquent($rejectedReason);

        $rejectedReasonEloquent->save();

        return RejectedReasonData::fromEloquent($rejectedReasonEloquent);
    }

    public function update(RejectedReason $rejectedReason): RejectedReasonData
    {
        $rejectedReasonEloquent = RejectedReasonMapper::toEloquent($rejectedReason);

        $rejectedReasonEloquent->save();

        return RejectedReasonData::fromEloquent($rejectedReasonEloquent);
    }

    public function updateOrder($rejectedReasons)
    {
        $decoded_rejected_reasons = json_decode($rejectedReasons);
        foreach ($decoded_rejected_reasons as $rejectedReason) {
            $rejectedReasonEloquent = RejectedReasonsEloquentModel::query()->findOrFail($rejectedReason->id);

            $rejectedReasonEloquent->index = $rejectedReason->index;

            $rejectedReasonEloquent->update();

        }

        return $rejectedReasons;
    }

    public function delete(int $rejectedReasonId): void
    {
        $rejectedReasonEloquent = RejectedReasonsEloquentModel::query()->findOrFail($rejectedReasonId);

        $rejectedReasonEloquent->delete();
    }
}
