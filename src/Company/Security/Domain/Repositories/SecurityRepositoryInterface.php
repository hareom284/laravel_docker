<?php

namespace Src\Company\Security\Domain\Repositories;


use Src\Company\Security\Application\DTO\PermissionData;
use Src\Company\Security\Application\DTO\RoleData;
use Src\Company\Security\Application\DTO\UserData;
use Src\Company\Security\Domain\Model\Entities\Permission;
use Src\Company\Security\Domain\Model\Entities\Role;
use Src\Company\Security\Domain\Model\User;

interface SecurityRepositoryInterface
{

    //get only user and id
    public function getUsersNameId();
    // get user
    public function getUsers($filters = []);

    // get only user name
    public function getUsersName();

    // store user
    public function createUser(User $user);



    //  update user
    public function updateUser(UserData $user);


    // server side rendering data for user
    public function filter($filters = []);


    // get permission
    public function getPermission($filters = []);

    // store permission
    public function createPermission(Permission $permission);

    //  update permission
    public function updatePermission(PermissionData $permission);

    // get roles
    public function getRole($filters = []);

    //get only roles name
    public function getRolesName();

    // store role
    public function createRole(Role $roleData);

    //  update role
    public function updateRole(RoleData $role);

    public function getUserForDashBoard();


    public function changepassword($request);
}
