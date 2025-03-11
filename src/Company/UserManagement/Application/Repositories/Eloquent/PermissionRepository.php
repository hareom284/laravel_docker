<?php

namespace Src\Company\UserManagement\Application\Repositories\Eloquent;

use Src\Company\System\Application\Mappers\PermissionMapper;
use Src\Company\UserManagement\Domain\Repositories\PermissionRepositoryInterface;
use Src\Company\UserManagement\Infrastructure\EloquentModels\PermissionEloquentModel;
use Illuminate\Support\Facades\Hash;
use Src\Company\System\Domain\Resources\PermissionResource;



class PermissionRepository implements PermissionRepositoryInterface
{
    //permission list
    public function getPermissions($filters = [])
    {
        $perPage = $filters['perPage'] ?? 10;

        $permissionEloquent = PermissionEloquentModel::filter($filters)->orderBy('id', 'desc')->paginate($perPage);

        $permissions = PermissionResource::collection($permissionEloquent);

        $links = [
            'first' => $permissions->url(1),
            'last' => $permissions->url($permissions->lastPage()),
            'prev' => $permissions->previousPageUrl(),
            'next' => $permissions->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $permissions->currentPage(),
            'from' => $permissions->firstItem(),
            'last_page' => $permissions->lastPage(),
            'path' => $permissions->url($permissions->currentPage()),
            'per_page' => $perPage,
            'to' => $permissions->lastItem(),
            'total' => $permissions->total(),
        ];
        $responseData['data'] = $permissions;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function permissionsWithoutPagi()
    {
        $permissionEloquent = PermissionEloquentModel::all();

        $permission = PermissionResource::collection($permissionEloquent);

        return $permission;
    }

}
