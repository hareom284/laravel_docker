<?php

namespace Src\Company\Security\Domain\Services;

use Src\Company\Security\Application\Requests\StoreRoleRequest;
use Src\Company\Security\Application\Mappers\RoleMapper;
use Src\Company\Security\Application\Requests\UpdateRoleRequest;
use Src\Company\Security\Application\UseCases\Commands\Role\StoreRoleCommand;
use Src\Company\Security\Application\DTO\RoleData;
use Src\Company\Security\Application\UseCases\Commands\Role\UpdateRoleCommand;

class RoleService
{

    /***
     *
     *  @return void
     *
     */
    public function createRole(StoreRoleRequest $request)
    {
        // Validate the incoming request data based on the specified rules in StoreRoleRequest
        $request->validated();

        // Create a new role using the
        $newRole = RoleMapper::fromRequest($request);
        $createNewRole = (new  StoreRoleCommand($newRole));
        $createNewRole->execute();
    }

    /**
     *  updateRole
     *
     */
    public function updateRole(UpdateRoleRequest $request, $role_id)
    {
        // Create a RoleData object from the request data and the ID of the role to be updated
        $roleData = RoleData::fromRequest($request, $role_id);

        // Create an instance of the UpdateRoleCommand with the role data
        $updateRole = (new UpdateRoleCommand($roleData));

        // Execute the update role command
        $updateRole->execute();
    }

    /**
     * deleteRole
     */
    public function deleteRole($role)
    {
        $role->delete();
    }
}
