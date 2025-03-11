<?php

namespace Src\Company\Project\Application\Mappers;

use Illuminate\Http\Request;
use Src\Company\Project\Domain\Model\Entities\Property;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyEloquentModel;
use Src\Company\Project\Application\DTO\PropertyData;
use Src\Company\Project\Application\DTO\PropertyTypeData;
use Src\Company\Project\Domain\Model\Entities\Project;
use Src\Company\Project\Domain\Model\Entities\PropertyType;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyTypeEloquentModel;

use function PHPSTORM_META\type;

class PropertyTypeMapper {
    
    public static function fromRequest(Request $request, ?int $type_id = null): PropertyType
    {
        return new PropertyType(
            id: $type_id,
            type: $request->type,
            is_predefined: 0,
        );
    }

    public static function fromEloquent(PropertyTypeEloquentModel $propertyTypeEloquent): PropertyTypeData
    {
        return new PropertyTypeData(
            id: $propertyTypeEloquent->id,
            type: $propertyTypeEloquent->type,
            is_predefined: $propertyTypeEloquent->is_predefined,
        );
    }

    public static function toEloquent(PropertyType $propertyType): PropertyTypeEloquentModel
    {
        $propertyTypeEloquent = new PropertyTypeEloquentModel();
        if($propertyType->id)
        {
            $propertyTypeEloquent = PropertyTypeEloquentModel::query()->findOrFail($propertyType->id);
        }
        $propertyTypeEloquent->type = $propertyType->type;
        $propertyTypeEloquent->is_predefined = $propertyType->is_predefined;

        return $propertyTypeEloquent;
    }

}