<?php

namespace Src\Company\UserManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\UserManagement\Domain\Model\Entities\Role;
use Src\Company\UserManagement\Domain\Repositories\RoleRepositoryInterface;

class StoreRoleCommand implements CommandInterface
{
    private RoleRepositoryInterface $repository;

    public function __construct(
        private readonly Role $role,
        private readonly array $permissionIds
    )
    {
        $this->repository = app()->make(RoleRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeRole', UserPolicy::class);
        return $this->repository->store($this->role,$this->permissionIds);
    }
}
