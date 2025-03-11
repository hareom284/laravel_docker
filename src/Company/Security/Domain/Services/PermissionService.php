<?php

namespace Src\Company\Security\Domain\Services;

use Src\Company\Security\Application\Mappers\PermissionMapper;
use Src\Company\Security\Application\Requests\StorepermissionRequest;
use Src\Company\Security\Application\UseCases\Commands\Permission\StorePermissionCommand;
use Src\Company\Security\Application\DTO\PermissionData;
use Src\Company\Security\Application\Requests\UpdatepermissionRequest;
use Src\Company\Security\Application\UseCases\Commands\Permission\UpdatePermissionCommand;

class PermissionService
{

    /**
     *  create Permission
     * @param
     *
     * @return void
     */
    public function createPermission(StorepermissionRequest $request)
    {
        $newPermission = PermissionMapper::fromRequest($request);
        $createNewPermission = (new StorePermissionCommand($newPermission));
        $createNewPermission->execute();
    }

    /**
     * update Permission
     *
     *  @param
     *
     *  @return void
     */

    public function updatePermission(UpdatepermissionRequest $request, $permission_id)
    {
        // Validate the request data
        $request->validated();

        // Call the userInterFace to update the permission

        $updatePermission = PermissionData::fromRequest($request, $permission_id);

        $updatePermission = (new UpdatePermissionCommand($updatePermission));
        $updatePermission->execute();
    }

    public function deletePermission($permission)
    {
        $permission->delete();
    }
}
