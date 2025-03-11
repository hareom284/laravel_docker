<?php

namespace Src\Company\UserManagement\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\System\Domain\Model\Entities\Permission;
use Src\Company\UserManagement\Infrastructure\EloquentModels\PermissionEloquentModel;

class PermissionMapper
{
    public static function fromRequest(Request $request, ?int $permission_id = null): Permission
    {
        return new Permission(
            id: $permission_id,
            name: $request->string('name'),
            description: $request->string('description')
        );
    }

    public static function fromEloquent(PermissionEloquentModel $permissionEloquent): Permission
    {
        return new Permission(
            id: $permissionEloquent->id,
            name: $permissionEloquent->name,
            description: $permissionEloquent->description
        );
    }

    public static function toEloquent(Permission $permission): PermissionEloquentModel
    {
        $permissionEloquent = new PermissionEloquentModel();
        if ($permission->id) {
            $permissionEloquent = PermissionEloquentModel::query()->findOrFail($permission->id);
        }
        $permissionEloquent->name = $permission->name;
        $permissionEloquent->description = $permission->description;
        return $permissionEloquent;
    }
}
