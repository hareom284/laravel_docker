<?php

namespace Src\Company\UserManagement\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Company\UserManagement\Application\DTO\RoleData;
use Src\Company\UserManagement\Application\Mappers\RoleMapper;
use Src\Company\UserManagement\Domain\Model\Entities\Role;
use Src\Company\UserManagement\Domain\Repositories\RoleRepositoryInterface;
use Src\Company\UserManagement\Infrastructure\EloquentModels\RoleEloquentModel;
use Src\Company\UserManagement\Domain\Resources\RoleResource;

class RoleRepository implements RoleRepositoryInterface
{
    public function getRoles($filters = [])
    {
        //roles list
        $perPage = $filters['perPage'] ?? 10;

        $roleEloquent = RoleEloquentModel::where('name', '!=', 'SuperAdmin')->filter($filters)->orderBy('id', 'desc')->paginate($perPage);

        $roles = RoleResource::collection($roleEloquent);

        $links = [
            'first' => $roles->url(1),
            'last' => $roles->url($roles->lastPage()),
            'prev' => $roles->previousPageUrl(),
            'next' => $roles->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $roles->currentPage(),
            'from' => $roles->firstItem(),
            'last_page' => $roles->lastPage(),
            'path' => $roles->url($roles->currentPage()),
            'per_page' => $perPage,
            'to' => $roles->lastItem(),
            'total' => $roles->total(),
        ];
        $responseData['data'] = $roles;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function findById(int $id)
    {
        $roleEloquent = RoleEloquentModel::query()->findOrFail($id);

        $role = new RoleResource($roleEloquent);

        return $role;
    }

    public function store(Role $role, $permissionIds): RoleData
    {
        return DB::transaction(function () use ($role, $permissionIds) {

            $roleEloquent = RoleMapper::toEloquent($role);

            $roleEloquent->save();

            // Attach permission IDs to the role using the role_permissions pivot table
            $roleEloquent->permissions()->attach($permissionIds);

            return RoleData::fromEloquent($roleEloquent);
        });
    }

    public function update(Role $role, $permissionIds): Role
    {
        $roleArray = $role->toArray();

        $roleEloquent = RoleEloquentModel::query()->findOrFail($role->id);

        $roleEloquent->fill($roleArray);

        $roleEloquent->save();

        $roleEloquent->permissions()->sync($permissionIds);

        return $role;
    }

    public function delete(int $role_id): void
    {
        $roleEloquent = RoleEloquentModel::query()->findOrFail($role_id);
        $roleEloquent->delete();
    }
}
