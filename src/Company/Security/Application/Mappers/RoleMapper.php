<?php

namespace Src\Company\Security\Application\Mappers;

use Illuminate\Http\Request;
use  Src\Company\Security\Domain\Model\Entities\Role;
use Src\Company\Security\Infrastructure\EloquentModels\RoleEloquentModel;

class RoleMapper
{
    public static function fromRequest(Request $request, $role_id = null): Role
    {
        return new Role(
            id: $role_id,
            name: $request->name,
            description: $request->description,
        );
    }

    public static function toEloquent(Role $role): RoleEloquentModel
    {
        $RoleEloquent = new RoleEloquentModel();

        if ($role->id) {
            $RoleEloquent = RoleEloquentModel::query()->findOrFail($role->id);
        }

        $RoleEloquent->name = $role->name;
        $RoleEloquent->description = $role->description;
        return $RoleEloquent;
    }
}
