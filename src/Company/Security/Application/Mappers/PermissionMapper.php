<?php

namespace Src\Company\Security\Application\Mappers;

use Illuminate\Http\Request;
use Src\Company\Security\Domain\Model\Entities\Permission;
use Src\Company\Security\Infrastructure\EloquentModels\PermissionEloquentModel;

class PermissionMapper
{
    public static function fromRequest(Request $request, $permission_id = null): Permission
    {
        return new Permission(
            id: $permission_id,
            name: $request->name,
            description: $request->description,
        );
    }


    public static function toEloquent(Permission $permission): PermissionEloquentModel
    {
        $PermissionElquent = new PermissionEloquentModel();

        if ($permission->id) {
            $PermissionElquent = PermissionEloquentModel::query()->findOrFail($permission->id);
        }

        $PermissionElquent->name = $permission->name;
        $PermissionElquent->description = $permission->description;
        return $PermissionElquent;
    }
}
