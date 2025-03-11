<?php

namespace Src\Company\Project\Application\Mappers;

use Illuminate\Http\Request;
use Src\Company\Project\Domain\Model\Entities\Property;
use Src\Company\Project\Infrastructure\EloquentModels\PropertyEloquentModel;
use Src\Company\Project\Application\DTO\PropertyData;
use Src\Company\Project\Domain\Model\Entities\Project;

use function PHPSTORM_META\type;

class PropertyMapper
{

    public static function fromRequest($request, ?int $property_id = null, ?int $type_id = null): Property
    {
        return new Property(
            id: $property_id,
            type_id: $type_id,
            street_name: $request->street_name ? $request->street_name : null,
            block_num: $request->block_num ? $request->block_num : null,
            unit_num: $request->unit_num ? $request->unit_num : null,
            postal_code: $request->postal_code ? $request->postal_code : null
        );
    }

    public static function fromEloquent(PropertyEloquentModel $propertyEloquent): PropertyData
    {
        return new PropertyData(
            id: $propertyEloquent->id,
            type_id: $propertyEloquent->type_id,
            street_name: $propertyEloquent->street_name,
            block_num: $propertyEloquent->block_num,
            unit_num: $propertyEloquent->unit_num,
            postal_code: $propertyEloquent->postal_code
        );
    }

    public static function toEloquent(Property $property): PropertyEloquentModel
    {
        $propertyEloquent = new PropertyEloquentModel();
        if ($property->id) {
            $propertyEloquent = PropertyEloquentModel::query()->findOrFail($property->id);
        }
        $propertyEloquent->type_id = $property->type_id;
        $propertyEloquent->street_name = $property->street_name;
        $propertyEloquent->block_num = $property->block_num;
        $propertyEloquent->unit_num = $property->unit_num;
        $propertyEloquent->postal_code = $property->postal_code;

        return $propertyEloquent;
    }

    public static function toEloquentFromProject(Project $project, ?int $property_id = null): PropertyEloquentModel
    {
        $propertyEloquent = new PropertyEloquentModel();
        $propertyEloquent->id = $property_id;
        $propertyEloquent->type = $project->type;
        $propertyEloquent->street_name = $project->street_name;
        $propertyEloquent->block_num = $project->block_num;
        $propertyEloquent->unit_num = $project->unit_num;
        $propertyEloquent->postal_code = $project->postal_code;

        return $propertyEloquent;
    }
}
