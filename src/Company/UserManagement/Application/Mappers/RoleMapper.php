<?php

namespace Src\Company\UserManagement\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\UserManagement\Domain\Model\Entities\Role;
use Src\Company\UserManagement\Infrastructure\EloquentModels\RoleEloquentModel;

class RoleMapper
{
    public static function fromRequest(Request $request, ?int $role_id = null): Role
    {
        return new Role(
            id: $role_id,
            name: $request->string('name'),
            description: $request->string('description')
        );
    }

    public static function fromEloquent(RoleEloquentModel $roleEloquent): Role
    {
        return new Role(
            id: $roleEloquent->id,
            name: $roleEloquent->name,
            description: $roleEloquent->description
        );
    }

    public static function toEloquent(Role $role): RoleEloquentModel
    {
        $roleEloquent = new RoleEloquentModel();
        if ($role->id) {
            $roleEloquent = RoleEloquentModel::query()->findOrFail($role->id);
        }
        $roleEloquent->name = $role->name;
        $roleEloquent->description = $role->description;
        return $roleEloquent;
    }
}
