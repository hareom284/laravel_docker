<?php

namespace Src\Company\Security\Application\UseCases\Commands\Role;


use Src\Common\Domain\CommandInterface;
use Src\Company\Security\Application\DTO\RoleData;
use Src\Company\Security\Domain\Repositories\SecurityRepositoryInterface;

class UpdateRoleCommand implements CommandInterface
{
    private SecurityRepositoryInterface $repository;

    public function __construct(
        private readonly RoleData $roleData
    )
    {
        $this->repository = app()->make(SecurityRepositoryInterface::class);
    }

    public function execute()
    {
        return $this->repository->updateRole($this->roleData);
    }
}
