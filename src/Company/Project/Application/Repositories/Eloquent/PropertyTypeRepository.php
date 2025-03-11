<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Src\Company\Project\Application\DTO\PropertyData;
use Src\Company\Project\Application\DTO\PropertyTypeData;
use Src\Company\Project\Application\Mappers\PropertyMapper;
use Src\Company\Project\Application\Mappers\PropertyTypeMapper;
use Src\Company\Project\Domain\Model\Entities\Property;
use Src\Company\Project\Domain\Model\Entities\PropertyType;
use Src\Company\Project\Domain\Repositories\PropertyRepositoryInterface;
use Src\Company\Project\Domain\Repositories\PropertyTypeRepositoryInterface;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyTypeEloquentModel;

class PropertyTypeRepository implements PropertyTypeRepositoryInterface
{

    public function index()
    {
        $propertyLists = PropertyTypeEloquentModel::query()->where('is_predefined',true)->get();

        return $propertyLists;
    }

    public function store($type)
    {
        $propertyType = PropertyTypeEloquentModel::query()->firstOrCreate(
            ['id' => $type],
            ['type' => $type]
        );

        return $propertyType;
    }

}