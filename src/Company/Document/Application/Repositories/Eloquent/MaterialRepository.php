<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Src\Company\Document\Domain\Repositories\MaterialRepositoryInterface;
use Src\Company\Document\Infrastructure\EloquentModels\MaterialEloquentModel;

class MaterialRepository implements MaterialRepositoryInterface
{

    public function getMaterials()
    {
        $materials = MaterialEloquentModel::query()->where('is_predefined',1)->get();

        return $materials;

    }
}