<?php

namespace Src\Company\Security\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Exception;
use Src\Company\Security\Domain\Resources\PermissionResource;
use Src\Company\Security\Domain\Resources\RoleResource;
use Src\Company\Security\Domain\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Src\Company\Organization\Infrastructure\EloquentModels\OrganizationEloquentModel;
use Src\Company\Security\Application\DTO\PermissionData;
use Src\Company\Security\Application\DTO\RoleData;
use Src\Company\Security\Application\DTO\UserData;
use Src\Company\Security\Application\Mappers\PermissionMapper;
use Src\Company\Security\Application\Mappers\RoleMapper;
use Src\Company\Security\Application\Mappers\UserMapper;
use Src\Company\Security\Domain\Model\Entities\Role;
use Src\Company\Security\Domain\Model\User;
use Src\Company\Security\Domain\Model\Entities\Permission;
use Src\Company\Security\Domain\Repositories\SecurityRepositoryInterface;
use Src\Company\Security\Infrastructure\EloquentModels\UserEloquentModel;
use Src\Company\Security\Infrastructure\EloquentModels\RoleEloquentModel;
use Src\Company\Security\Infrastructure\EloquentModels\PermissionEloquentModel;

class SecurityRepository implements SecurityRepositoryInterface
{

    //get only user name and i
    public function getUsersNameId()
    {
        $user_names = UserEloquentModel::get();
        return $user_names;
    }
    // get user
    public function getUsers($filters = [])
    {
        //set roles
        $users = UserResource::collection(UserEloquentModel::filter($filters)
            ->with('roles')
            ->orderBy('id', 'desc')
            ->paginate($filters['perPage'] ?? 10));


        return $users;
    }


    //get only user name
    public function getUsersName()
    {
        $user_names = UserEloquentModel::pluck('name');
        return $user_names;
    }
    // store user
    public function createUser(User $user)
    {

        $userEloquent = UserMapper::toEloquent($user);
        $userEloquent->save();
        if (request()->hasFile('image') && request()->file('image')->isValid()) {
            $userEloquent->addMediaFromRequest('image')->toMediaCollection('image', 'media_user');
        }

        $userEloquent->roles()->sync(request('role'));
    }

    //  update user
    public function updateUser(UserData $user)
    {

        $userArray = $user->toArray();
        $updateUserEloquent = UserEloquentModel::query()->findOrFail($user->id);
        $updateUserEloquent->fill($userArray);
        $updateUserEloquent->save();

        //  delete image if reupload or insert if does not exit
        if (request()->hasFile('image') && request()->file('image')->isValid()) {

            $old_image = $updateUserEloquent->getFirstMedia('image');
            if ($old_image != null) {
                $old_image->delete();

                $updateUserEloquent->addMediaFromRequest('image')->toMediaCollection('image', 'media_user');
            } else {

                $updateUserEloquent->addMediaFromRequest('image')->toMediaCollection('image', 'media_user');
            }
        }


        $updateUserEloquent->roles()->sync(request('role'));
    }
    //user filter
    public function filter($filters = [])
    {
        $query = UserEloquentModel::query();

        // Add filters to the query
        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        if (isset($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        // Return the filtered results
        return $query;
    }

    // get permission
    public function getPermission($filters = [])
    {

        $permissions = PermissionResource::collection(PermissionEloquentModel::filter($filters)->orderBy('id', 'desc')->paginate($filters['perPage'] ?? 10));

        $default_permissions = PermissionEloquentModel::orderBy('id', 'desc')->get();
        return [
            "permissions" => $permissions,
            "default_permissions" => $default_permissions
        ];
    }

    // store permission
    public function createPermission(Permission $permission)
    {
        $newPermissionEloquent = PermissionMapper::toEloquent($permission);
        $newPermissionEloquent->save();
    }

    // update permission

    public function updatePermission(PermissionData $permission)
    {
        $permissionArray = $permission->toArray();
        $permissionEloquent = PermissionEloquentModel::query()->findOrFail($permission->id);
        $permissionEloquent->fill($permissionArray);
        $permissionEloquent->save();
    }

    // get roles
    public function getRole($filters = [])
    {
        $paginate_roles = RoleResource::collection(RoleEloquentModel::filter($filters)->with('permissions')->orderBy('id', 'desc')->paginate($filters['perPage'] ?? 10));
        $default_roles = RoleEloquentModel::with('permissions')->get();
        return [
            "paginate_roles" => $paginate_roles,
            "default_roles" => $default_roles
        ];
    }

    //get only roles name
    public function getRolesName()
    {
        $roles_name = RoleEloquentModel::get()->prepend('Select');
        return $roles_name;
    }


    // store role
    public function createRole(Role $role)
    {

        $RoleEloquent = RoleMapper::toEloquent($role);
        $RoleEloquent->save();
        $RoleEloquent->permissions()->sync(request('selectedIds'));
    }

    //  update role
    public function updateRole(RoleData $role)
    {

        $roleArray = $role->toArray();
        $roleEloquent = RoleEloquentModel::query()->findOrFail($role->id);
        $roleEloquent->fill($roleArray);
        $roleEloquent->save();
        $roleEloquent->permissions()->sync(request('selectedIds'));
    }

    public function getUserForDashBoard()
    {
        $users = UserEloquentModel::with('roles')->latest()->take(5)->get();
        $organizations = OrganizationEloquentModel::with('plan')->latest()->take(5)->get();
        return [$users, $organizations];
    }

    public function changepassword($request)
    {
        $user = Auth::user();
        //  check passord same or not
        if (Hash::check($request->currentpassword, $user->password)) {
            UserEloquentModel::find($user->id)->update([
                "password" => $request->updatedpassword
            ]);

            return true;
        }
        return false;
    }
}
