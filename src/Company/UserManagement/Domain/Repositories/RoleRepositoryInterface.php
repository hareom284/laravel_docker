<?php

namespace Src\Company\UserManagement\Domain\Repositories;
use Src\Company\UserManagement\Domain\Model\Entities\Role;
use Src\Company\UserManagement\Application\DTO\RoleData;

interface RoleRepositoryInterface
{
    public function getRoles($filters = []);

    public function findById(int $id);

    public function store(Role $role,$permissionIds): RoleData;

    public function update(Role $role,$permissionIds): Role;

    public function delete(int $role_id): void;

}
