<?php

namespace Src\Company\UserManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\UserManagement\Domain\Policies\UserPolicy;
use Src\Company\System\Domain\Repositories\RoleRepositoryInterface;

class DeleteRoleCommand implements CommandInterface
{
    private RoleRepositoryInterface $repository;

    public function __construct(
        private readonly int $role_id
    )
    {
        $this->repository = app()->make(RoleRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('deleteRole', UserPolicy::class);
        return $this->repository->delete($this->role_id);
    }
}
