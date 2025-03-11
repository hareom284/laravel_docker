<?php

namespace Src\Company\CustomerManagement\Application\Repositories\Eloquent;

use Src\Company\CustomerManagement\Domain\Repositories\IdMilestoneRepositoryMobileInterface;
use Src\Company\CustomerManagement\Domain\Resources\IdMilestoneResources;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\IdMilestonesEloquentModel;

class IdMilestoneRepositoryMobile implements IdMilestoneRepositoryMobileInterface
{

    public function findAllIdMilestones()
    {
        $idMilestoneEloquent = IdMilestonesEloquentModel::query()->orderBy('index', 'asc')->get();

        $idMilestones = IdMilestoneResources::collection($idMilestoneEloquent);

        return $idMilestones;
    }

}
