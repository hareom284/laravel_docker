<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Src\Company\Document\Domain\Repositories\EvoMobileRepositoryInterface;
use Src\Company\Document\Infrastructure\EloquentModels\EvoEloquentModel;


class EvoMobileRepository implements EvoMobileRepositoryInterface
{

    public function getEvoAmt($projectId)
    {
        $evo = EvoEloquentModel::where('project_id', $projectId)->whereNotNull('signed_date')->get();

        return $evo;
    }

}
