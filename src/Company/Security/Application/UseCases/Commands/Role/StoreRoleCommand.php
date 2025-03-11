<?php

namespace Src\Company\Security\Application\UseCases\Commands\Role;


use Src\Common\Domain\CommandInterface;
use Src\Company\Security\Domain\Model\Entities\Role;
use Src\Company\Security\Domain\Repositories\SecurityRepositoryInterface;

class StoreRoleCommand implements CommandInterface
{
    private SecurityRepositoryInterface $repository;

    public function __construct(
        private readonly Role $role
    )
    {
        $this->repository = app()->make(SecurityRepositoryInterface::class);
    }

    public function execute()
    {
        return $this->repository->createRole($this->role);
    }
}
