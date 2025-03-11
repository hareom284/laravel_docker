<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Src\Company\Project\Application\DTO\PropertyData;
use Src\Company\Project\Application\Mappers\PropertyMapper;
use Src\Company\Project\Domain\Model\Entities\Property;
use Src\Company\Project\Domain\Repositories\PropertyRepositoryInterface;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyEloquentModel;

class PropertyRepository implements PropertyRepositoryInterface
{

    public function index()
    {
        $properties = PropertyEloquentModel::query()->all();

        return $properties;
    }

    public function store(Property $property): PropertyData
    {
        $propertyeloquent = PropertyMapper::toEloquent($property);

        $propertyeloquent->save();

        return PropertyMapper::fromEloquent($propertyeloquent);
    }

    public function update(Property $property) : PropertyData {
        $propertyeloquent = PropertyMapper::toEloquent($property);

        $propertyeloquent->save();

        return PropertyMapper::fromEloquent($propertyeloquent);
    }

    public function destroy(int $property_id) : void {

        $propertyeloquent = PropertyEloquentModel::query()->findOrFail($property_id);

        $propertyeloquent->delete();
    }

}