<?php

namespace Src\Company\UserManagement\Domain\Repositories;


interface PermissionRepositoryInterface
{
    public function getPermissions($filters = []);

    public function permissionsWithoutPagi();

}
