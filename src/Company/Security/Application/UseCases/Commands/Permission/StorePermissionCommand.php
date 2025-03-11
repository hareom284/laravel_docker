<?php

namespace Src\Company\Security\Application\UseCases\Commands\Permission;

use Src\Company\Security\Domain\Model\Entities\Permission;
use Src\Company\Security\Domain\Repositories\SecurityRepositoryInterface;
use Src\Common\Domain\CommandInterface;

class StorePermissionCommand implements CommandInterface
{
    private SecurityRepositoryInterface $repository;

    public function __construct(
        private readonly Permission $permission
    )
    {
        $this->repository = app()->make(SecurityRepositoryInterface::class);
    }

    public function execute()
    {
        return $this->repository->createPermission($this->permission);
    }
}
