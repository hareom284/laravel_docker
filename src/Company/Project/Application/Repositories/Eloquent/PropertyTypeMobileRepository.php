<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;


use Src\Company\Project\Domain\Repositories\PropertyTypeMobileRepositoryInterface;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyTypeEloquentModel;

class PropertyTypeMobileRepository implements PropertyTypeMobileRepositoryInterface
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